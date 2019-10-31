<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Shell\Command\CommandInterface;
use PHPUnit\Framework\TestCase;

class ShellResultTest extends TestCase
{
    /**
     * Get parameters.
     *
     * Test if all the get parameters return the given construct values.
     *
     * @covers \ExtendsFramework\Shell\ShellResult::__construct()
     * @covers \ExtendsFramework\Shell\ShellResult::getCommand()
     * @covers \ExtendsFramework\Shell\ShellResult::getData()
     */
    public function testGetParameters(): void
    {
        $command = $this->createMock(CommandInterface::class);

        $result = new ShellResult($command, ['foo' => 'bar']);

        $this->assertSame($command, $result->getCommand());
        $this->assertSame(['foo' => 'bar'], $result->getData());
    }
}
