<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Framework\ServiceLocator\Loader;

use ExtendsFramework\Console\Framework\ServiceLocator\Factory\ShellFactory;
use ExtendsFramework\Console\Input\InputInterface;
use ExtendsFramework\Console\Input\Posix\PosixInput;
use ExtendsFramework\Console\Output\OutputInterface;
use ExtendsFramework\Console\Output\Posix\PosixOutput;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\Invokable\InvokableResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\ShellInterface;

class ShellConfigLoader implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return [
            ServiceLocatorInterface::class => [
                FactoryResolver::class => [
                    ShellInterface::class => ShellFactory::class,
                ],
                InvokableResolver::class => [
                    InputInterface::class => PosixInput::class,
                    OutputInterface::class => PosixOutput::class,
                ],
            ],
        ];
    }
}
