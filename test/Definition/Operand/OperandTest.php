<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition\Operand;

use PHPUnit\Framework\TestCase;

class OperandTest extends TestCase
{
    /**
     * Get name.
     *
     * Test if all the get parameters return the given construct values.
     *
     * @covers \ExtendsFramework\Shell\Definition\Operand\Operand::__construct()
     * @covers \ExtendsFramework\Shell\Definition\Operand\Operand::getName()
     */
    public function testGetParameters(): void
    {
        $operand = new Operand('fooBar');

        $this->assertSame('fooBar', $operand->getName());
    }
}
