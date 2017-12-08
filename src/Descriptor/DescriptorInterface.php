<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Descriptor;

use ExtendsFramework\Console\Shell\About\AboutInterface;
use ExtendsFramework\Console\Shell\Command\CommandInterface;
use ExtendsFramework\Console\Shell\Definition\DefinitionInterface;
use Throwable;

interface DescriptorInterface
{
    /**
     * Describe shell.
     *
     * @param AboutInterface      $about
     * @param DefinitionInterface $definition
     * @param CommandInterface[]  $commands
     * @param bool|null           $short
     * @return DescriptorInterface
     */
    public function shell(AboutInterface $about, DefinitionInterface $definition, array $commands, bool $short = null): DescriptorInterface;

    /**
     * Describe $command.
     *
     * @param AboutInterface   $about
     * @param CommandInterface $command
     * @param bool|null        $short
     * @return DescriptorInterface
     */
    public function command(AboutInterface $about, CommandInterface $command, bool $short = null): DescriptorInterface;

    /**
     * Suggest given $command.
     *
     * @param CommandInterface|null $command
     * @return DescriptorInterface
     */
    public function suggest(CommandInterface $command = null): DescriptorInterface;

    /**
     * Describe $exception.
     *
     * @param Throwable $exception
     * @return DescriptorInterface
     */
    public function exception(Throwable $exception): DescriptorInterface;

    /**
     * Set verbosity.
     *
     * With a higher $verbosity, output will be more verbose.
     *
     * @param int $verbosity
     * @return DescriptorInterface
     */
    public function setVerbosity(int $verbosity): DescriptorInterface;
}
