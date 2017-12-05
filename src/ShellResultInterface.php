<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Shell\Command\CommandInterface;

interface ShellResultInterface
{
    /**
     * Get matched command.
     *
     * @return CommandInterface
     */
    public function getCommand(): CommandInterface;

    /**
     * Get parsed data.
     *
     * @return array
     */
    public function getData(): array;
}
