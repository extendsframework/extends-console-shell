<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Shell\About\AboutInterface;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Definition\Definition;
use ExtendsFramework\Shell\Definition\DefinitionException;
use ExtendsFramework\Shell\Definition\DefinitionInterface;
use ExtendsFramework\Shell\Definition\Option\Exception\NoShortAndLongName;
use ExtendsFramework\Shell\Definition\Option\Option;
use ExtendsFramework\Shell\Descriptor\DescriptorInterface;
use ExtendsFramework\Shell\Exception\CommandNotFound;
use ExtendsFramework\Shell\Parser\ParserException;
use ExtendsFramework\Shell\Parser\ParserInterface;
use ExtendsFramework\Shell\Suggester\SuggesterInterface;

class Shell implements ShellInterface
{
    /**
     * Shell and command descriptor.
     *
     * @var DescriptorInterface
     */
    private $descriptor;

    /**
     * Command suggester.
     *
     * @var SuggesterInterface
     */
    private $suggester;

    /**
     * Parser to use for arguments.
     *
     * @var ParserInterface
     */
    private $parser;

    /**
     * Shell about information.
     *
     * @var AboutInterface
     */
    private $about;

    /**
     * Shell definition for global options.
     *
     * @var DefinitionInterface
     */
    private $definition;

    /**
     * Commands to iterate.
     *
     * @var CommandInterface[]
     */
    private $commands = [];

    /**
     * Create a new Shell.
     *
     * @param DescriptorInterface $descriptor
     * @param SuggesterInterface  $suggester
     * @param ParserInterface     $parser
     * @param AboutInterface      $about
     */
    public function __construct(
        DescriptorInterface $descriptor,
        SuggesterInterface $suggester,
        ParserInterface $parser,
        AboutInterface $about
    ) {
        $this->descriptor = $descriptor;
        $this->suggester = $suggester;
        $this->parser = $parser;
        $this->about = $about;
    }

    /**
     * @inheritDoc
     * @throws NoShortAndLongName
     */
    public function process(array $arguments): ?ShellResultInterface
    {
        try {
            $defaults = $this
                ->getParser()
                ->parse($this->getDefinition(), $arguments, false);
        } catch (ParserException | DefinitionException $exception) {
            $this
                ->getDescriptor()
                ->exception($exception)
                ->shell($this->getAbout(), $this->getDefinition(), $this->getCommands(), true);

            return null;
        }

        $remaining = $defaults->getRemaining();
        $parsed = $defaults->getParsed();

        $this
            ->getDescriptor()
            ->setVerbosity($parsed['verbose'] ?? 1);

        $name = array_shift($remaining);
        if ($name === null) {
            $this
                ->getDescriptor()
                ->shell($this->getAbout(), $this->getDefinition(), $this->getCommands());

            return null;
        }

        try {
            $command = $this->getCommand($name);
        } catch (CommandNotFound $exception) {
            $this
                ->getDescriptor()
                ->exception($exception)
                ->suggest(
                    $this
                        ->getSuggester()
                        ->suggest($name, ...$this->getCommands())
                )
                ->shell($this->getAbout(), $this->getDefinition(), $this->getCommands(), true);

            return null;
        }

        $help = $parsed['help'] ?? false;
        if ($help) {
            $this
                ->getDescriptor()
                ->command($this->getAbout(), $command);

            return null;
        }

        try {
            $result = $this
                ->getParser()
                ->parse(
                    $command->getDefinition(),
                    $remaining
                );

            return new ShellResult(
                $command,
                $result->getParsed()
            );
        } catch (ParserException | DefinitionException $exception) {
            $this
                ->getDescriptor()
                ->exception($exception)
                ->command($this->getAbout(), $command, true);

            return null;
        }
    }

    /**
     * Add $command to shell.
     *
     * Commands will be processed in chronological order.
     *
     * @param CommandInterface $command
     * @return Shell
     */
    public function addCommand(CommandInterface $command): Shell
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * Get command with $name.
     *
     * @param string $name
     * @return CommandInterface
     * @throws CommandNotFound When command can not be found.
     */
    private function getCommand(string $name): CommandInterface
    {
        foreach ($this->commands as $command) {
            if ($command->getName() === $name) {
                return $command;
            }
        }

        throw new CommandNotFound($name);
    }

    /**
     * Get definition for default options.
     *
     * @return DefinitionInterface
     * @throws NoShortAndLongName
     */
    private function getDefinition(): DefinitionInterface
    {
        if ($this->definition === null) {
            $this->definition = (new Definition())
                ->addOption(new Option('verbose', 'Be more verbose.', 'v', 'verbose', true, true))
                ->addOption(new Option('help', 'Show help about shell or command.', 'h', 'help'));
        }

        return $this->definition;
    }

    /**
     * Get descriptor.
     *
     * @return DescriptorInterface
     */
    private function getDescriptor(): DescriptorInterface
    {
        return $this->descriptor;
    }

    /**
     * Get suggester.
     *
     * @return SuggesterInterface
     */
    private function getSuggester(): SuggesterInterface
    {
        return $this->suggester;
    }

    /**
     * Get parser.
     *
     * @return ParserInterface
     */
    private function getParser(): ParserInterface
    {
        return $this->parser;
    }

    /**
     * Get about.
     *
     * @return AboutInterface
     */
    private function getAbout(): AboutInterface
    {
        return $this->about;
    }

    /**
     * Get commands.
     *
     * @return CommandInterface[]
     */
    private function getCommands(): array
    {
        return $this->commands;
    }
}
