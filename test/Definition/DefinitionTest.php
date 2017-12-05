<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition;

use ExtendsFramework\Shell\Definition\Operand\OperandInterface;
use ExtendsFramework\Shell\Definition\Option\OptionInterface;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    /**
     * Add and get.
     *
     * Test that options and operands can be added and received.
     *
     * @covers \ExtendsFramework\Shell\Definition\Definition::addOption()
     * @covers \ExtendsFramework\Shell\Definition\Definition::addOperand()
     * @covers \ExtendsFramework\Shell\Definition\Definition::getOption()
     * @covers \ExtendsFramework\Shell\Definition\Definition::getOperand()
     * @covers \ExtendsFramework\Shell\Definition\Definition::getOptions()
     * @covers \ExtendsFramework\Shell\Definition\Definition::getOperands()
     */
    public function testAddAndGet(): void
    {
        $short = $this->createMock(OptionInterface::class);
        $short
            ->expects($this->exactly(1))
            ->method('getShort')
            ->willReturn('f');

        $short
            ->expects($this->once())
            ->method('getLong')
            ->willReturn(null);

        $long = $this->createMock(OptionInterface::class);

        $long
            ->expects($this->once())
            ->method('getLong')
            ->willReturn('force');

        $operand = $this->createMock(OperandInterface::class);

        /**
         * @var OptionInterface  $short
         * @var OptionInterface  $long
         * @var OperandInterface $operand
         */
        $definition = new Definition();
        $definition
            ->addOption($short)
            ->addOption($long)
            ->addOperand($operand)
            ->addOperand($operand);

        $this->assertSame($short, $definition->getOption('f'));
        $this->assertSame($long, $definition->getOption('force', true));
        $this->assertSame($operand, $definition->getOperand(1));

        $this->assertContainsOnlyInstancesOf(OptionInterface::class, $definition->getOptions());
        $this->assertContainsOnlyInstancesOf(OperandInterface::class, $definition->getOperands());
    }

    /**
     * Short option not found.
     *
     * Test that short option ('f') can not be found and an exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Shell\Definition\Definition::getOption()
     * @covers                   \ExtendsFramework\Shell\Definition\Exception\OptionNotFound::__construct()
     * @expectedException        \ExtendsFramework\Shell\Definition\Exception\OptionNotFound
     * @expectedExceptionMessage No short option found for name "-f".
     */
    public function testOptionNotFound(): void
    {
        $definition = new Definition();
        $definition->getOption('f');
    }

    /**
     * Long option not found.
     *
     * Test that long option ('force') can not be found and an exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Shell\Definition\Definition::getOption()
     * @covers                   \ExtendsFramework\Shell\Definition\Exception\OptionNotFound::__construct()
     * @expectedException        \ExtendsFramework\Shell\Definition\Exception\OptionNotFound
     * @expectedExceptionMessage No long option found for name "--force".
     */
    public function testCanNotGetLongOption(): void
    {
        $definition = new Definition();
        $definition->getOption('force', true);
    }

    /**
     * Operand not found.
     *
     * Test that long option for position (0) can not be found and an exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Shell\Definition\Definition::getOperand()
     * @covers                   \ExtendsFramework\Shell\Definition\Exception\OperandNotFound::__construct()
     * @expectedException        \ExtendsFramework\Shell\Definition\Exception\OperandNotFound
     * @expectedExceptionMessage No operand found for position "0".
     */
    public function testCanNotGetOperand(): void
    {
        $definition = new Definition();
        $definition->getOperand(0);
    }
}