<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Framework\ServiceLocator\Loader;

use ExtendsFramework\Console\Framework\ServiceLocator\Factory\ShellFactory;
use ExtendsFramework\Console\Input\InputInterface;
use ExtendsFramework\Console\Input\Posix\PosixInput;
use ExtendsFramework\Console\Output\OutputInterface;
use ExtendsFramework\Console\Output\Posix\PosixOutput;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\Invokable\InvokableResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\ShellInterface;
use PHPUnit\Framework\TestCase;

class ShellConfigLoaderTest extends TestCase
{
    /**
     * Load.
     *
     * Test that loader returns correct array.
     *
     * @covers \ExtendsFramework\Shell\Framework\ServiceLocator\Loader\ShellConfigLoader::load()
     */
    public function testLoad(): void
    {
        $loader = new ShellConfigLoader();

        $this->assertSame([
            ServiceLocatorInterface::class => [
                FactoryResolver::class => [
                    ShellInterface::class => ShellFactory::class,
                ],
                InvokableResolver::class => [
                    InputInterface::class => PosixInput::class,
                    OutputInterface::class => PosixOutput::class,
                ],
            ],
        ], $loader->load());
    }
}
