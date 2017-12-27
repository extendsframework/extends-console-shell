<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Console\Output\Posix\PosixOutput;
use ExtendsFramework\Shell\About\About;
use ExtendsFramework\Shell\Command\Command;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Definition\Definition;
use ExtendsFramework\Shell\Definition\Operand\Operand;
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
    protected $name;

    /**
     * Command to run shell.
     *
     * @var string|null
     */
    protected $program;

    /**
     * Shell version.
     *
     * @var string|null
     */
    protected $version;

    /**
     * Shell descriptor.
     *
     * @var DescriptorInterface|null
     */
    protected $descriptor;

    /**
     * Command suggester.
     *
     * @var SuggesterInterface|null
     */
    protected $suggester;

    /**
     * Argument parser.
     *
     * @var ParserInterface|null
     */
    protected $parser;

    /**
     * Commands.
     *
     * @var CommandInterface[]
     */
    protected $commands = [];

    /**
     * @inheritDoc
     */
    public function build(): ShellInterface
    {
        $shell = new Shell(
            $this->getDescriptor(),
            $this->getSuggester(),
            $this->getParser(),
            new About(
                $this->getName(),
                $this->getProgram(),
                $this->getVersion()
            )
        );

        foreach ($this->getCommands() as $command) {
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
     */
    public function addCommand(
        string $name,
        string $description,
        array $operands = null,
        array $options = null,
        array $parameters = null
    ): ShellBuilder {
        $definition = new Definition();
        foreach ($operands ?? [] as $operand) {
            $definition->addOperand(
                new Operand($operand['name'])
            );
        }

        foreach ($options ?? [] as $option) {
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

        $this->commands[] = new Command(
            $name,
            $description,
            $definition,
            $parameters
        );

        return $this;
    }

    /**
     * Set shell name.
     *
     * @param null|string $name
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
     * @param null|string $program
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
     * @param null|string $version
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
     * Get commands.
     *
     * @return CommandInterface[]
     */
    protected function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Get shell name.
     *
     * @return null|string
     */
    protected function getName(): ?string
    {
        return $this->name ?: 'Extends Framework Console';
    }

    /**
     * Get program to run shell.
     *
     * @return null|string
     */
    protected function getProgram(): ?string
    {
        return $this->program ?: 'extends';
    }

    /**
     * Get shell version.
     *
     * @return null|string
     */
    protected function getVersion(): ?string
    {
        return $this->version ?: '0.1';
    }

    /**
     * Get shell descriptor.
     *
     * @return DescriptorInterface|null
     */
    protected function getDescriptor(): ?DescriptorInterface
    {
        return $this->descriptor ?: new Descriptor(new PosixOutput());
    }

    /**
     * Get command suggester.
     *
     * @return SuggesterInterface|null
     */
    protected function getSuggester(): ?SuggesterInterface
    {
        return $this->suggester ?: new SimilarTextSuggester();
    }

    /**
     * Get argument parser.
     *
     * @return ParserInterface|null
     */
    protected function getParser(): ?ParserInterface
    {
        return $this->parser ?: new PosixParser();
    }

    /**
     * Reset builder after build.
     *
     * @return ShellBuilder
     */
    protected function reset(): ShellBuilder
    {
        $this->name = $this->program = $this->version = $this->descriptor = $this->suggester = $this->parser = null;
        $this->commands = [];

        return $this;
    }
}
