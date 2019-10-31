<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Framework\ServiceLocator\Factory;

use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\ShellInterface;
use PHPUnit\Framework\TestCase;

class ShellFactoryTest extends TestCase
{
    /**
     * Create service.
     *
     * Test that factory will return an ShellInterface instance.
     *
     * @covers \ExtendsFramework\Shell\Framework\ServiceLocator\Factory\ShellFactory::createService()
     */
    public function testCreateService(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('getConfig')
            ->willReturn([
                ShellInterface::class => [
                    'name' => 'Fancy shell name.',
                    'program' => 'run',
                    'version' => '1.3',
                    'commands' => [
                        [
                            'name' => 'task1',
                            'description' => 'Fancy task 1',
                            'operands' => [
                                [
                                    'name' => 'first_name',
                                ],
                            ],
                            'options' => [
                                [
                                    'name' => 'force',
                                    'description' => 'Force creation.',
                                    'short' => 'f',
                                ],
                            ],
                        ],
                        [
                            'name' => 'task2',
                            'description' => 'Fancy task 2',
                            'operands' => [
                                [
                                    'name' => 'last_name',
                                ],
                            ],
                            'options' => [
                                [
                                    'name' => 'activate',
                                    'description' => 'Activate user.',
                                    'long' => 'activate',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $factory = new ShellFactory();
        $shell = $factory->createService(ShellInterface::class, $serviceLocator, []);

        $this->assertInstanceOf(ShellInterface::class, $shell);
    }
}
