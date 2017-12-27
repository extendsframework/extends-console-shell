<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Framework\ServiceLocator\Loader;

use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\Framework\ServiceLocator\Factory\ShellFactory;
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
            ],
        ];
    }
}
