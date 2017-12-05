<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Parser\Posix;

use ArrayIterator;
use ExtendsFramework\Shell\Definition\DefinitionException;
use ExtendsFramework\Shell\Definition\DefinitionInterface;
use ExtendsFramework\Shell\Definition\Operand\OperandInterface;
use ExtendsFramework\Shell\Definition\Option\OptionInterface;
use ExtendsFramework\Shell\Parser\ParseResult;
use ExtendsFramework\Shell\Parser\ParseResultInterface;
use ExtendsFramework\Shell\Parser\ParserInterface;
use ExtendsFramework\Shell\Parser\Posix\Exception\ArgumentNotAllowed;
use ExtendsFramework\Shell\Parser\Posix\Exception\MissingArgument;
use ExtendsFramework\Shell\Parser\Posix\Exception\MissingOperand;

class PosixParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(DefinitionInterface $definition, array $arguments, bool $strict = null): ParseResultInterface
    {
        $strict = $strict ?? true;

        $result = $this->parseArguments($definition, $arguments, $strict);
        if ($strict === true) {
            $parsed = $result->getParsed();

            foreach ($definition->getOperands() as $operand) {
                $name = $operand->getName();
                if (array_key_exists($name, $parsed) === false) {
                    throw new MissingOperand($name);
                }
            }
        }

        return $result;
    }

    /**
     * Parse $arguments against $definition in $strict mode.
     *
     * @param DefinitionInterface $definition
     * @param array               $arguments
     * @param bool                $strict
     * @return ParseResultInterface
     * @throws ArgumentNotAllowed
     * @throws DefinitionException
     * @throws MissingArgument
     * @see http://pubs.opengroup.org/onlinepubs/9699919799/basedefs/V1_chap12.html
     */
    protected function parseArguments(DefinitionInterface $definition, array &$arguments, bool $strict): ParseResultInterface
    {
        $operandPosition = 0;
        $terminated = false;
        $remaining = [];
        $parsed = [];

        $iterator = new ArrayIterator($arguments);
        foreach ($iterator as $argument) {
            $argument = trim($argument);

            if ($terminated === true) {
                $operand = $this->getOperand($definition, $operandPosition++, $strict);
                if ($operand instanceof OperandInterface) {
                    $parsed[$operand->getName()] = $argument;
                } else {
                    $remaining[] = $argument;
                }
            } elseif ($argument === '--') {
                $terminated = true;
            } elseif (strpos($argument, '--') === 0) {
                $long = substr($argument, 2);
                $long = explode('=', $long, 2);
                $hasArgument = isset($long[1]);

                $option = $this->getOption($definition, $long[0], true, $strict);
                if ($option instanceof OptionInterface) {
                    $name = $option->getName();
                    if ($option->isFlag() === true) {
                        if ($hasArgument === true) {
                            throw new ArgumentNotAllowed($option, true);
                        }

                        if ($option->isMultiple() === true) {
                            $parsed[$name] = ($parsed[$name] ?? 0) + 1;
                        } else {
                            $parsed[$name] = true;
                        }
                    } elseif ($hasArgument === true) {
                        $parsed[$name] = $long[1];
                    } else {
                        $iterator->next();
                        if ($iterator->valid() === true) {
                            $parsed[$name] = $iterator->current();
                        } elseif ($option->isFlag() === false) {
                            throw new MissingArgument($option, true);
                        }
                    }
                } else {
                    $remaining[] = $argument;
                }
            } elseif (strpos($argument, '-') === 0) {
                $short = substr($argument, 1);

                $parts = str_split($short);
                foreach ($parts as $index => $part) {
                    $option = $this->getOption($definition, $part, false, $strict);
                    if ($option instanceof OptionInterface) {
                        $name = $option->getName();
                        if ($option->isFlag() === true) {
                            if ($option->isMultiple() === true) {
                                $parsed[$name] = ($parsed[$name] ?? 0) + 1;
                            } else {
                                $parsed[$name] = true;
                            }
                        } elseif (count($parts) > ($index + 1)) {
                            $value = implode(array_slice($parts, $index + 1));
                            if (strpos($value, '=') === 0) {
                                $value = substr($value, 1);
                            }

                            $parsed[$name] = $value;

                            break;
                        } else {
                            $iterator->next();
                            if ($iterator->valid() === true) {
                                $parsed[$name] = $iterator->current();
                            } elseif ($option->isFlag() === false) {
                                throw new MissingArgument($option);
                            }
                        }
                    } else {
                        $remaining[] = '-' . implode('', array_slice($parts, $index));

                        break;
                    }
                }
            } else {
                $operand = $this->getOperand($definition, $operandPosition++, $strict);
                if ($operand instanceof OperandInterface) {
                    $parsed[$operand->getName()] = $argument;
                } else {
                    $remaining[] = $argument;
                }
            }
        }

        return new ParseResult($parsed, $remaining, $strict);
    }

    /**
     * Get operand at $position from $definition.
     *
     * When $strict is true, an exception will be (re)thrown when operand doesn't exist.
     *
     * @param DefinitionInterface $definition
     * @param int                 $position
     * @param bool                $strict
     * @return OperandInterface|null
     * @throws DefinitionException
     */
    protected function getOperand(DefinitionInterface $definition, int $position, bool $strict): ?OperandInterface
    {
        try {
            $operand = $definition->getOperand($position);
        } catch (DefinitionException $exception) {
            if ($strict === true) {
                throw $exception;
            }
        }

        return $operand ?? null;
    }

    /**
     * Get option $name from $definition.
     *
     * When $strict is true, an exception will be (re)thrown when option doesn't exist.
     *
     * @param DefinitionInterface $definition
     * @param string              $name
     * @param bool                $long
     * @param bool                $strict
     * @return OptionInterface|null
     * @throws DefinitionException
     */
    protected function getOption(DefinitionInterface $definition, string $name, bool $long, bool $strict): ?OptionInterface
    {
        try {
            $option = $definition->getOption($name, $long);
        } catch (DefinitionException $exception) {
            if ($strict === true) {
                throw $exception;
            }
        }

        return $option ?? null;
    }
}
