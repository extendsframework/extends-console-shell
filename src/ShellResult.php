<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell;

use ExtendsFramework\Console\Shell\Command\CommandInterface;

class ShellResult implements ShellResultInterface
{
    /**
     * Matched command.
     *
     * @var CommandInterface
     */
    protected $command;

    /**
     * Parsed data for command.
     *
     * @var array
     */
    protected $data;

    /**
     * Create new shell result.
     *
     * @param CommandInterface $command
     * @param                  $data
     */
    public function __construct(CommandInterface $command, array $data)
    {
        $this->command = $command;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->data;
    }
}
