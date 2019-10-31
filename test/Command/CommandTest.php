<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Command;

use ExtendsFramework\Shell\Definition\DefinitionInterface;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    /**
     * Get parameters.
     *
     * Test if all the get parameters return the given construct values.
     *
     * @covers \ExtendsFramework\Shell\Command\Command::__construct()
     * @covers \ExtendsFramework\Shell\Command\Command::getName()
     * @covers \ExtendsFramework\Shell\Command\Command::getDescription()
     * @covers \ExtendsFramework\Shell\Command\Command::getDefinition()
     * @covers \ExtendsFramework\Shell\Command\Command::getParameters()
     */
    public function testGetParameters(): void
    {
        $definition = $this->createMock(DefinitionInterface::class);

        /**
         * @var DefinitionInterface $definition
         */
        $command = new Command('do.task', 'Some fancy task!', $definition, ['foo' => 'bar']);

        $this->assertSame('do.task', $command->getName());
        $this->assertSame('Some fancy task!', $command->getDescription());
        $this->assertSame($definition, $command->getDefinition());
        $this->assertSame(['foo' => 'bar'], $command->getParameters());
    }
}
