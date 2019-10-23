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
    protected $descriptor;

    /**
     * Command suggester.
     *
     * @var SuggesterInterface
     */
    protected $suggester;

    /**
     * Parser to use for arguments.
     *
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Shell about information.
     *
     * @var AboutInterface
     */
    protected $about;

    /**
     * Shell definition for global options.
     *
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * Commands to iterate.
     *
     * @var CommandInterface[]
     */
    protected $commands;

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
        $this->commands = [];
    }

    /**
     * @inheritDoc
     * @throws NoShortAndLongName
     */
    public function process(array $arguments): ?ShellResultInterface
    {
        $about = $this->getAbout();
        $parser = $this->getParser();
        $commands = $this->getCommands();
        $definition = $this->getDefinition();
        $descriptor = $this->getDescriptor();

        try {
            $defaults = $parser->parse($definition, $arguments, false);
        } catch (ParserException | DefinitionException $exception) {
            $descriptor
                ->exception($exception)
                ->shell($about, $definition, $commands, true);

            return null;
        }

        $remaining = $defaults->getRemaining();
        $parsed = $defaults->getParsed();

        $descriptor->setVerbosity($parsed['verbose'] ?? 1);

        $name = array_shift($remaining);
        if ($name === null) {
            $descriptor->shell($about, $definition, $commands);

            return null;
        }

        try {
            $command = $this->getCommand($name);
        } catch (CommandNotFound $exception) {
            $descriptor
                ->exception($exception)
                ->suggest(
                    $this
                        ->getSuggester()
                        ->suggest($name, ...$commands)
                )
                ->shell($about, $definition, $commands, true);

            return null;
        }

        $help = $parsed['help'] ?? false;
        if ($help === true) {
            $descriptor->command($about, $command);

            return null;
        }

        try {
            $result = $parser->parse(
                $command->getDefinition(),
                $remaining
            );

            return new ShellResult(
                $command,
                $result->getParsed()
            );
        } catch (ParserException | DefinitionException $exception) {
            $descriptor
                ->exception($exception)
                ->command($about, $command, true);

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
    protected function getCommand(string $name): CommandInterface
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
    protected function getDefinition(): DefinitionInterface
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
    protected function getDescriptor(): DescriptorInterface
    {
        return $this->descriptor;
    }

    /**
     * Get suggester.
     *
     * @return SuggesterInterface
     */
    protected function getSuggester(): SuggesterInterface
    {
        return $this->suggester;
    }

    /**
     * Get parser.
     *
     * @return ParserInterface
     */
    protected function getParser(): ParserInterface
    {
        return $this->parser;
    }

    /**
     * Get about.
     *
     * @return AboutInterface
     */
    protected function getAbout(): AboutInterface
    {
        return $this->about;
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
}
