<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Definition;

use ExtendsFramework\Console\Shell\Definition\Exception\OperandNotFound;
use ExtendsFramework\Console\Shell\Definition\Exception\OptionNotFound;
use ExtendsFramework\Console\Shell\Definition\Operand\OperandInterface;
use ExtendsFramework\Console\Shell\Definition\Option\OptionInterface;

class Definition implements DefinitionInterface
{
    /**
     * Options to iterate.
     *
     * @var OptionInterface[]
     */
    protected $options = [];

    /**
     * Operands to iterate.
     *
     * @var OperandInterface[]
     */
    protected $operands = [];

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function getOperands(): array
    {
        return $this->operands;
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $name, bool $long = null): OptionInterface
    {
        foreach ($this->options as $option) {
            if ($long) {
                if ($option->getLong() === $name) {
                    return $option;
                }
            } elseif ($option->getShort() === $name) {
                return $option;
            }
        }

        throw new OptionNotFound($name, $long);
    }

    /**
     * @inheritDoc
     */
    public function getOperand(int $position): OperandInterface
    {
        if (isset($this->operands[$position])) {
            return $this->operands[$position];
        }

        throw new OperandNotFound($position);
    }

    /**
     * Add $operand to definition.
     *
     * @param OperandInterface $operand
     * @return Definition
     */
    public function addOperand(OperandInterface $operand): Definition
    {
        $this->operands[] = $operand;

        return $this;
    }

    /**
     * Add $option to definition.
     *
     * @param OptionInterface $option
     * @return Definition
     */
    public function addOption(OptionInterface $option): Definition
    {
        $this->options[] = $option;

        return $this;
    }
}
