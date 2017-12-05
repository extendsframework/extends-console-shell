<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

interface ShellBuilderInterface
{
    /**
     * Build shell.
     *
     * @return ShellInterface
     */
    public function build(): ShellInterface;
}
