<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Framework\ServiceLocator\Loader;

use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Console\Shell\Framework\ServiceLocator\Factory\ShellFactory;
use ExtendsFramework\Console\Shell\ShellInterface;
use PHPUnit\Framework\TestCase;

class ShellConfigLoaderTest extends TestCase
{
    /**
     * Load.
     *
     * Test that loader returns correct array.
     *
     * @covers \ExtendsFramework\Console\Shell\Framework\ServiceLocator\Loader\ShellConfigLoader::load()
     */
    public function testLoad(): void
    {
        $loader = new ShellConfigLoader();

        $this->assertSame([
            ServiceLocatorInterface::class => [
                FactoryResolver::class => [
                    ShellInterface::class => ShellFactory::class,
                ],
            ],
        ], $loader->load());
    }
}
