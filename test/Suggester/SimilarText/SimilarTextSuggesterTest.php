<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Suggester\SimilarText;

use ExtendsFramework\Shell\Command\CommandInterface;
use PHPUnit\Framework\TestCase;

class SimilarTextSuggesterTest extends TestCase
{
    /**
     * Best match.
     *
     * Test that suggester can suggest command ('do.task') for phrase ('d_task').
     *
     * @covers \ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester::__construct()
     * @covers \ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester::suggest()
     */
    public function testBestMatch(): void
    {
        $command1 = $this->createMock(CommandInterface::class);
        $command1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('some.task');

        $command2 = $this->createMock(CommandInterface::class);
        $command2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $suggester = new SimilarTextSuggester();
        $suggestion = $suggester->suggest('d_task', ...[
            $command1,
            $command2,
        ]);

        $this->assertSame($command2, $suggestion);
    }

    /**
     * Exact match.
     *
     * Test that suggester can suggest command ('some.task') for phrase ('some.task').
     *
     * @covers \ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester::__construct()
     * @covers \ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester::suggest()
     */
    public function testExactMatch(): void
    {
        $command1 = $this->createMock(CommandInterface::class);
        $command1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('some.task');

        $command2 = $this->createMock(CommandInterface::class);
        $command2
            ->expects($this->never())
            ->method('getName');

        $suggester = new SimilarTextSuggester();
        $suggestion = $suggester->suggest('some.task', ...[
            $command1,
            $command2,
        ]);

        $this->assertSame($command1, $suggestion);
    }

    /**
     * No match.
     *
     * Test that suggester can not suggest a command for phrase ('foo.bar').
     *
     * @covers \ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester::__construct()
     * @covers \ExtendsFramework\Shell\Suggester\SimilarText\SimilarTextSuggester::suggest()
     */
    public function testNoMatch(): void
    {
        $command1 = $this->createMock(CommandInterface::class);
        $command1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('some.task');

        $command2 = $this->createMock(CommandInterface::class);
        $command2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $suggester = new SimilarTextSuggester();
        $suggestion = $suggester->suggest('foo.bar', ...[
            $command1,
            $command2,
        ]);

        $this->assertNull($suggestion);
    }
}
