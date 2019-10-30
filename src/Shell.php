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

        try {
            $this->definition = (new Definition())
                ->addOption(new Option('verbose', 'Be more verbose.', 'v', 'verbose', true, true))
                ->addOption(new Option('help', 'Show help about shell or command.', 'h', 'help'));
        } catch (NoShortAndLongName $exception) {
        }
    }

    /**
     * @inheritDoc
     */
    public function process(array $arguments): ?ShellResultInterface
    {
        try {
            $defaults = $this->parser->parse($this->definition, $arguments, false);
        } catch (ParserException | DefinitionException $exception) {
            $this->descriptor
                ->exception($exception)
                ->shell($this->about, $this->definition, $this->commands, true);

            return null;
        }

        $remaining = $defaults->getRemaining();
        $parsed = $defaults->getParsed();

        $this->descriptor->setVerbosity($parsed['verbose'] ?? 1);

        $name = array_shift($remaining);
        if ($name === null) {
            $this->descriptor->shell($this->about, $this->definition, $this->commands);

            return null;
        }

        try {
            $command = $this->getCommand($name);
        } catch (CommandNotFound $exception) {
            $this->descriptor
                ->exception($exception)
                ->suggest(
                    $this->suggester->suggest($name, ...$this->commands)
                )
                ->shell($this->about, $this->definition, $this->commands, true);

            return null;
        }

        $help = $parsed['help'] ?? false;
        if ($help) {
            $this->descriptor->command($this->about, $command);

            return null;
        }

        try {
            $result = $this->parser->parse(
                $command->getDefinition(),
                $remaining
            );

            return new ShellResult(
                $command,
                $result->getParsed()
            );
        } catch (ParserException | DefinitionException $exception) {
            $this->descriptor
                ->exception($exception)
                ->command($this->about, $command, true);

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
}
