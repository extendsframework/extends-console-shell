<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Descriptor;

use ExtendsFramework\Console\Formatter\Color\Red\Red;
use ExtendsFramework\Console\Formatter\Color\Yellow\Yellow;
use ExtendsFramework\Console\Formatter\FormatterException;
use ExtendsFramework\Console\Output\OutputInterface;
use ExtendsFramework\Shell\About\AboutInterface;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Definition\DefinitionInterface;
use ExtendsFramework\Shell\Definition\Option\OptionInterface;
use Throwable;

class Descriptor implements DescriptorInterface
{
    /**
     * Output to send description to.
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Create a new descriptor.
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritDoc
     * @throws FormatterException When foreground color is not supported.
     */
    public function shell(
        AboutInterface $about,
        DefinitionInterface $definition,
        array $commands,
        bool $short = null
    ): DescriptorInterface {
        if ($short) {
            $this->output
                ->newLine()
                ->line(sprintf(
                    'See \'%s --help\' for more information about available commands and options.',
                    $about->getProgram()
                ));

            return $this;
        }

        $this->output
            ->line(sprintf(
                '%s (version %s)',
                $about->getName(),
                $about->getVersion()
            ))
            ->newLine()
            ->line('Usage:')
            ->newLine()
            ->text(
                sprintf(
                    '%s ',
                    $about->getProgram()
                ),
                $this->output
                    ->getFormatter()
                    ->setForeground(new Yellow())
                    ->setFixedWidth(strlen($about->getProgram()) + 1)
                    ->setTextIndent(2)
            )
            ->line('<command> [<arguments>] [<options>]')
            ->newLine()
            ->line('Commands:')
            ->newLine();

        if (empty($commands)) {
            $this->output->line(
                'No commands defined.',
                $this->output
                    ->getFormatter()
                    ->setForeground(new Yellow())
                    ->setTextIndent(2)
            );
        } else {
            foreach ($commands as $command) {
                if ($command instanceof CommandInterface) {
                    $this->output
                        ->text(
                            $command->getName(),
                            $this->output
                                ->getFormatter()
                                ->setForeground(new Yellow())
                                ->setFixedWidth(22)
                                ->setTextIndent(2)
                        )
                        ->line($command->getDescription());
                }
            }
        }

        $this->output
            ->newLine()
            ->line('Options:')
            ->newLine();

        foreach ($definition->getOptions() as $option) {
            if ($option instanceof OptionInterface) {
                $notation = $this->getOptionNotation($option);
                $this->output
                    ->text(
                        $notation,
                        $this->output
                            ->getFormatter()
                            ->setForeground(new Yellow())
                            ->setFixedWidth(22)
                            ->setTextIndent(2)
                    )
                    ->line($option->getDescription());
            }
        }

        $this->output
            ->newLine()
            ->line(sprintf(
                'See \'%s <command> --help\' for more information about a command.',
                $about->getProgram()
            ));

        return $this;
    }

    /**
     * @inheritDoc
     * @throws FormatterException When foreground color is not supported.
     */
    public function command(AboutInterface $about, CommandInterface $command, bool $short = null): DescriptorInterface
    {
        $short = $short ?? false;
        $definition = $command->getDefinition();

        if ($short) {
            $this->output
                ->newLine()
                ->line(sprintf(
                    'See \'%s %s --help\' for more information about the command.',
                    $about->getProgram(),
                    $command->getName()
                ));

            return $this;
        }

        $this->output
            ->line(sprintf(
                '%s (version %s)',
                $about->getName(),
                $about->getVersion()
            ))
            ->newLine()
            ->line('Usage:')
            ->newLine()
            ->text(
                sprintf(
                    '%s ',
                    $about->getProgram()
                ),
                $this->output
                    ->getFormatter()
                    ->setForeground(new Yellow())
                    ->setFixedWidth(strlen($about->getProgram()) + 1)
                    ->setTextIndent(2)
            )
            ->text(sprintf(
                '%s ',
                $command->getName()
            ));

        $operands = $definition->getOperands();
        if (!empty($operands)) {
            foreach ($operands as $operand) {
                $this->output->text(sprintf(
                    '<%s> ',
                    $operand->getName()
                ));
            }
        }

        $options = $definition->getOptions();
        if (!empty($options)) {
            $this->output
                ->line('[<options>] ')
                ->newLine()
                ->line('Options:')
                ->newLine();

            foreach ($options as $option) {
                $notation = $this->getOptionNotation($option);
                $this->output
                    ->text(
                        $notation,
                        $this->output
                            ->getFormatter()
                            ->setForeground(new Yellow())
                            ->setFixedWidth(22)
                            ->setTextIndent(2)
                    )
                    ->line($option->getDescription());
            }
        } else {
            $this->output->newLine();
        }

        $this->output
            ->newLine()
            ->line(sprintf(
                'See \'%s --help\' for more information about this shell and default options.',
                $about->getProgram()
            ));

        return $this;
    }

    /**
     * @inheritDoc
     * @throws FormatterException When foreground color is not supported.
     */
    public function suggest(CommandInterface $command = null): DescriptorInterface
    {
        if ($command instanceof CommandInterface) {
            $this->output
                ->newLine()
                ->text('Did you mean "')
                ->text(
                    $command->getName(),
                    $this->output
                        ->getFormatter()
                        ->setForeground(new Yellow())
                )
                ->line('"?');
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws FormatterException When foreground color is not supported.
     */
    public function exception(Throwable $exception): DescriptorInterface
    {
        $this->output->line(
            $exception->getMessage(),
            $this->output
                ->getFormatter()
                ->setForeground(new Red())
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setVerbosity(int $verbosity): DescriptorInterface
    {
        $this->output->setVerbosity($verbosity);

        return $this;
    }

    /**
     * Get option notation.
     *
     * @param OptionInterface $option
     * @return string
     */
    private function getOptionNotation(OptionInterface $option): string
    {
        $multiple = $option->isMultiple();
        $short = $option->getShort();
        $long = $option->getLong();
        $flag = $option->isFlag();

        $notation = '';
        if ($short !== null) {
            $notation .= '-' . $short;

            if (!$flag) {
                $notation .= '=';
            } elseif ($multiple) {
                $notation .= '+';
            }
        }

        if ($long !== null) {
            if ($notation !== '') {
                $notation .= '|';
            }

            $notation .= '--' . $long;

            if (!$flag) {
                $notation .= '=';
            } elseif ($multiple) {
                $notation .= '+';
            }
        }

        return $notation;
    }
}
