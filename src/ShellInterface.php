<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

interface ShellInterface
{
    /**
     * Match $arguments to corresponding command.
     *
     * When $arguments can not be matched, null will be returned.
     *
     * @param array $arguments
     * @return ShellResultInterface|null
     */
    public function process(array $arguments): ?ShellResultInterface;
}
