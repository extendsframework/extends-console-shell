<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Definition\Option;

use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    /**
     * Get parameters.
     *
     * Test if all the get parameters return the given construct values.
     *
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::__construct()
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::getName()
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::getDescription()
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::getShort()
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::getLong()
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::isFlag()
     * @covers \ExtendsFramework\Console\Shell\Definition\Option\Option::isMultiple()
     */
    public function testGetParameters(): void
    {
        $option = new Option('fooBar', 'Some description.', 'f', 'foo-bar', true, true);

        static::assertSame('fooBar', $option->getName());
        static::assertSame('Some description.', $option->getDescription());
        static::assertSame('f', $option->getShort());
        static::assertSame('foo-bar', $option->getLong());
        static::assertSame(true, $option->isFlag());
        static::assertSame(true, $option->isMultiple());
    }

    /**
     * Short nor long.
     *
     * Test that an exception will be thrown when both short and long arguments are missing.
     *
     * @covers                   \ExtendsFramework\Console\Shell\Definition\Option\Option::__construct()
     * @covers                   \ExtendsFramework\Console\Shell\Definition\Option\Exception\NoShortAndLongName::__construct
     * @expectedException        \ExtendsFramework\Console\Shell\Definition\Option\Exception\NoShortAndLongName
     * @expectedExceptionMessage Option "fooBar" requires at least a short or long name, both not given.
     */
    public function testShortNorLong(): void
    {
        new Option('fooBar', 'Some description.', null, null, true, true);
    }
}
