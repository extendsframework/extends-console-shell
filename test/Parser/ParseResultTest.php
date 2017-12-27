<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Parser;

use PHPUnit\Framework\TestCase;

class ParseResultTest extends TestCase
{
    /**
     * Get parameters.
     *
     * Test if all the get parameters return the given construct values.
     *
     * @covers \ExtendsFramework\Shell\Parser\ParseResult::__construct()
     * @covers \ExtendsFramework\Shell\Parser\ParseResult::getParsed()
     * @covers \ExtendsFramework\Shell\Parser\ParseResult::getRemaining()
     * @covers \ExtendsFramework\Shell\Parser\ParseResult::isStrict()
     */
    public function testGetParameters(): void
    {
        $parsed = ['foo' => 'bar'];

        $remaining = ['qux' => 'quux'];

        $result = new ParseResult($parsed, $remaining, true);

        $this->assertSame($parsed, $result->getParsed());
        $this->assertSame($remaining, $result->getRemaining());
        $this->assertTrue($result->isStrict());
    }
}
