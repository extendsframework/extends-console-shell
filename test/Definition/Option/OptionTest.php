<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition\Option;

use ExtendsFramework\Shell\Definition\Option\Exception\NoShortAndLongName;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    /**
     * Get parameters.
     *
     * Test if all the get parameters return the given construct values.
     *
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::__construct()
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::getName()
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::getDescription()
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::getShort()
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::getLong()
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::isFlag()
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::isMultiple()
     */
    public function testGetParameters(): void
    {
        $option = new Option('fooBar', 'Some description.', 'f', 'foo-bar', true, true);

        $this->assertSame('fooBar', $option->getName());
        $this->assertSame('Some description.', $option->getDescription());
        $this->assertSame('f', $option->getShort());
        $this->assertSame('foo-bar', $option->getLong());
        $this->assertTrue($option->isFlag());
        $this->assertTrue($option->isMultiple());
    }

    /**
     * Short nor long.
     *
     * Test that an exception will be thrown when both short and long arguments are missing.
     *
     * @covers \ExtendsFramework\Shell\Definition\Option\Option::__construct()
     * @covers \ExtendsFramework\Shell\Definition\Option\Exception\NoShortAndLongName::__construct
     */
    public function testShortNorLong(): void
    {
        $this->expectException(NoShortAndLongName::class);
        $this->expectExceptionMessage('Option "fooBar" requires at least a short or long name, both not given.');

        new Option('fooBar', 'Some description.', null, null, true, true);
    }
}
