<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Console\Output\Posix\PosixOutput;
use ExtendsFramework\Shell\About\About;
use ExtendsFramework\Shell\Command\Command;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Definition\Definition;
use ExtendsFramework\Shell\Definition\Operand\Operand;
use ExtendsFramework\Shell\Definition\Option\Exception\NoShortAndLongName;
use ExtendsFramework\Shell\Definition\Option\Option;
use ExtendsFramework\Shell\Descriptor\Descriptor;
use ExtendsFramework\Shell\Descriptor\DescriptorInterface;
use ExtendsFramework\Shell\Parser\ParserInterface;
use ExtendsFramework\Shell\Parser\Posix\PosixParser;
use ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester;
use ExtendsFramework\Shell\Suggester\SuggesterInterface;

class ShellBuilder implements ShellBuilderInterface
{
    /**
     * Shell name.
     *
     * @var string|null
     */
    private $name;

    /**
     * Command to run shell.
     *
     * @var string|null
     */
    private $program;

    /**
     * Shell version.
     *
     * @var string|null
     */
    private $version;

    /**
     * Shell descriptor.
     *
     * @var DescriptorInterface|null
     */
    private $descriptor;

    /**
     * Command suggester.
     *
     * @var SuggesterInterface|null
     */
    private $suggester;

    /**
     * Argument parser.
     *
     * @var ParserInterface|null
     */
    private $parser;

    /**
     * Commands.
     *
     * @var CommandInterface[]
     */
    private $commands;

    /**
     * @inheritDoc
     */
    public function build(): ShellInterface
    {
        $shell = new Shell(
            $this->descriptor ?: new Descriptor(new PosixOutput()),
            $this->suggester ?: new SimilarTextSuggester(),
            $this->parser ?: new PosixParser(),
            new About(
                $this->name ?: 'Extends Framework Console',
                $this->program ?: 'extends',
                $this->version ?: '0.1'
            )
        );

        foreach ($this->commands as $command) {
            $shell->addCommand($command);
        }

        $this->reset();

        return $shell;
    }

    /**
     * Add command to shell.
     *
     * @param string     $name
     * @param string     $description
     * @param array|null $operands
     * @param array|null $options
     * @param array|null $parameters
     * @return ShellBuilder
     * @throws NoShortAndLongName When both short and long name are not given.
     */
    public function addCommand(
        string $name,
        string $description,
        array $operands = null,
        array $options = null,
        array $parameters = null
    ): ShellBuilder {
        $definition = new Definition();
        foreach ($operands ?: [] as $operand) {
            $definition->addOperand(
                new Operand($operand['name'])
            );
        }

        foreach ($options ?: [] as $option) {
            $definition->addOption(
                new Option(
                    $option['name'],
                    $option['description'],
                    $option['short'] ?? null,
                    $option['long'] ?? null,
                    $option['flag'] ?? null,
                    $option['multiple'] ?? null
                )
            );
        }

        $this->commands[] = new Command($name, $description, $definition, $parameters);

        return $this;
    }

    /**
     * Set shell name.
     *
     * @param string|null $name
     * @return ShellBuilder
     */
    public function setName($name = null): ShellBuilder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set command to run shell.
     *
     * @param string|null $program
     * @return ShellBuilder
     */
    public function setProgram($program = null): ShellBuilder
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Set shell version.
     *
     * @param string|null $version
     * @return ShellBuilder
     */
    public function setVersion($version = null): ShellBuilder
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set shell descriptor.
     *
     * @param DescriptorInterface|null $descriptor
     * @return ShellBuilder
     */
    public function setDescriptor($descriptor = null): ShellBuilder
    {
        $this->descriptor = $descriptor;

        return $this;
    }

    /**
     * Set command suggester.
     *
     * @param SuggesterInterface|null $suggester
     * @return ShellBuilder
     */
    public function setSuggester($suggester = null): ShellBuilder
    {
        $this->suggester = $suggester;

        return $this;
    }

    /**
     * Set argument parser.
     *
     * @param ParserInterface|null $parser
     * @return ShellBuilder
     */
    public function setParser($parser = null): ShellBuilder
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Reset builder after build.
     *
     * @return ShellBuilder
     */
    private function reset(): ShellBuilder
    {
        $this->name = null;
        $this->program = null;
        $this->version = null;
        $this->descriptor = null;
        $this->suggester = null;
        $this->parser = null;
        $this->commands = [];

        return $this;
    }
}
