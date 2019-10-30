<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Shell\About\AboutInterface;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Definition\DefinitionInterface;
use ExtendsFramework\Shell\Descriptor\DescriptorInterface;
use ExtendsFramework\Shell\Exception\CommandNotFound;
use ExtendsFramework\Shell\Parser\ParseResultInterface;
use ExtendsFramework\Shell\Parser\ParserInterface;
use ExtendsFramework\Shell\Parser\Posix\Exception\ArgumentNotAllowed;
use ExtendsFramework\Shell\Suggester\SuggesterInterface;
use PHPUnit\Framework\TestCase;

class ShellTest extends TestCase
{
    /**
     * Invalid default parameter.
     *
     * Test if the descriptor will be called to describe an exception and the shell when a default parameter
     * ('--help=true') is invalid.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::process()
     */
    public function testInvalidDefaultParameter(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);
        $exception = $this->createMock(ArgumentNotAllowed::class);

        $descriptor = $this->createMock(DescriptorInterface::class);
        $descriptor
            ->expects($this->once())
            ->method('exception')
            ->with($exception)
            ->willReturnSelf();

        $descriptor
            ->expects($this->once())
            ->method('shell')
            ->willReturnSelf();

        /**
         * @var ArgumentNotAllowed $exception
         */
        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->once())
            ->method('parse')
            ->willThrowException($exception);

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell->process([
            '--help=true',
        ]);

        $this->assertNull($result);
    }

    /**
     * No remaining arguments.
     *
     * Test if the descriptor is called to describe the shell when no remaining arguments left.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::process()
     */
    public function testNoRemainingArguments(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);

        $descriptor = $this->createMock(DescriptorInterface::class);
        $descriptor
            ->expects($this->once())
            ->method('shell')
            ->with(
                $about,
                $this->isInstanceOf(DefinitionInterface::class),
                []
            )
            ->willReturnSelf();

        $result = $this->createMock(ParseResultInterface::class);
        $result
            ->expects($this->once())
            ->method('getRemaining')
            ->willReturn([]);

        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->once())
            ->method('parse')
            ->willReturn($result);

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell->process([
            '--help',
        ]);

        $this->assertNull($result);
    }

    /**
     * Verbosity.
     *
     * Test that verbosity will be set to 3.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::process()
     */
    public function testVerbosity(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);

        $descriptor = $this->createMock(DescriptorInterface::class);
        $descriptor
            ->expects($this->once())
            ->method('setVerbosity')
            ->with(3)
            ->willReturnSelf();

        $result = $this->createMock(ParseResultInterface::class);
        $result
            ->expects($this->once())
            ->method('getParsed')
            ->willReturn([
                'verbose' => 3,
            ]);

        $result
            ->expects($this->once())
            ->method('getRemaining')
            ->willReturn([]);

        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->once())
            ->method('parse')
            ->willReturn($result);

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell->process([
            '-v',
            '-v',
            '-v',
        ]);

        $this->assertNull($result);
    }

    /**
     * Command not found.
     *
     * Test if the descriptor is called to describe an exception and shell when no command can be found.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::process()
     * @covers \ExtendsFramework\Shell\Exception\CommandNotFound::__construct()
     */
    public function testCommandNotFound(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);

        $descriptor = $this->createMock(DescriptorInterface::class);
        $descriptor
            ->expects($this->once())
            ->method('exception')
            ->with(
                $this->isInstanceOf(CommandNotFound::class)
            )
            ->willReturnSelf();

        $descriptor
            ->expects($this->once())
            ->method('suggest')
            ->with(null)
            ->willReturnSelf();

        $descriptor
            ->expects($this->once())
            ->method('shell')
            ->with(
                $about,
                $this->isInstanceOf(DefinitionInterface::class),
                [],
                true
            )
            ->willReturnSelf();

        $result = $this->createMock(ParseResultInterface::class);
        $result
            ->expects($this->once())
            ->method('getRemaining')
            ->willReturn([
                'do.task',
            ]);

        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->once())
            ->method('parse')
            ->willReturn($result);

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell->process([
            'do.task',
        ]);

        $this->assertNull($result);
    }

    /**
     * Help for command.
     *
     * Test if the descriptor is called to describe the given command.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::addCommand()
     * @covers \ExtendsFramework\Shell\Shell::process()
     */
    public function testHelpForCommand(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $descriptor = $this->createMock(DescriptorInterface::class);
        $descriptor
            ->expects($this->once())
            ->method('command')
            ->with($about, $command)
            ->willReturnSelf();

        $result = $this->createMock(ParseResultInterface::class);
        $result
            ->expects($this->once())
            ->method('getRemaining')
            ->willReturn([
                'do.task',
            ]);

        $result
            ->expects($this->once())
            ->method('getParsed')
            ->willReturn([
                'help' => true,
            ]);

        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->once())
            ->method('parse')
            ->willReturn($result);

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var CommandInterface    $command
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell
            ->addCommand($command)
            ->process([
                'do.task',
                '--help',
            ]);

        $this->assertNull($result);
    }

    /**
     * Matched command.
     *
     * Test that a command can be matched and the result is returned.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::addCommand()
     * @covers \ExtendsFramework\Shell\Shell::process()
     */
    public function testMatchedCommand(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);
        $definition = $this->createMock(DefinitionInterface::class);
        $descriptor = $this->createMock(DescriptorInterface::class);

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $command
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturnOnConsecutiveCalls($definition);

        $defaults = $this->createMock(ParseResultInterface::class);
        $defaults
            ->expects($this->once())
            ->method('getRemaining')
            ->willReturn([
                'do.task',
                'John Doe',
            ]);

        $defaults
            ->expects($this->once())
            ->method('getParsed')
            ->willReturn([]);

        $result = $this->createMock(ParseResultInterface::class);
        $result
            ->expects($this->once())
            ->method('getParsed')
            ->willReturn([
                'name' => 'John Doe',
            ]);

        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->exactly(2))
            ->method('parse')
            ->withConsecutive(
                [
                    $this->isInstanceOf(DefinitionInterface::class),
                    [
                        'do.task',
                        'John Doe',
                    ],
                    false,
                ],
                [
                    $definition,
                    [
                        'John Doe',
                    ],
                ]
            )
            ->willReturnOnConsecutiveCalls(
                $defaults,
                $result
            );

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var CommandInterface    $command
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell
            ->addCommand($command)
            ->process([
                'do.task',
                'John Doe',
            ]);

        $this->assertInstanceOf(ShellResultInterface::class, $result);
        if ($result instanceof ShellResultInterface) {
            $this->assertSame($command, $result->getCommand());
            $this->assertSame([
                'name' => 'John Doe',
            ], $result->getData());
        }
    }

    /**
     * Failed command.
     *
     * Test if the descriptor is called to describe an exception and shell when parsing the command fails.
     *
     * @covers \ExtendsFramework\Shell\Shell::__construct()
     * @covers \ExtendsFramework\Shell\Shell::addCommand()
     * @covers \ExtendsFramework\Shell\Shell::process()
     * @covers \ExtendsFramework\Shell\Shell::getCommand()
     */
    public function testFailedCommand(): void
    {
        $about = $this->createMock(AboutInterface::class);
        $suggester = $this->createMock(SuggesterInterface::class);
        $definition = $this->createMock(DefinitionInterface::class);
        $exception = $this->createMock(ArgumentNotAllowed::class);

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $command
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturnOnConsecutiveCalls($definition);

        $descriptor = $this->createMock(DescriptorInterface::class);
        $descriptor
            ->expects($this->once())
            ->method('exception')
            ->with($exception)
            ->willReturnSelf();

        $descriptor
            ->expects($this->once())
            ->method('command')
            ->with($about, $command, true)
            ->willReturnSelf();

        $defaults = $this->createMock(ParseResultInterface::class);
        $defaults
            ->expects($this->once())
            ->method('getRemaining')
            ->willReturn([
                'do.task',
                'John Doe',
            ]);

        $defaults
            ->expects($this->once())
            ->method('getParsed')
            ->willReturn([]);

        /**
         * @var ArgumentNotAllowed $exception
         */
        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->exactly(2))
            ->method('parse')
            ->withConsecutive(
                [
                    $this->isInstanceOf(DefinitionInterface::class),
                    [
                        'do.task',
                        'John Doe',
                    ],
                    false,
                ],
                [
                    $definition,
                    [
                        'John Doe',
                    ],
                ]
            )
            ->willReturnOnConsecutiveCalls(
                $defaults,
                $this->throwException($exception)
            );

        /**
         * @var DescriptorInterface $descriptor
         * @var SuggesterInterface  $suggester
         * @var ParserInterface     $parser
         * @var CommandInterface    $command
         * @var AboutInterface      $about
         */
        $shell = new Shell($descriptor, $suggester, $parser, $about);
        $result = $shell
            ->addCommand($command)
            ->process([
                'do.task',
                'John Doe',
            ]);

        $this->assertNull($result);
    }
}
