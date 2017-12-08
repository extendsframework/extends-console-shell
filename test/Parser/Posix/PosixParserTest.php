<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Parser\Posix;

use ExtendsFramework\Console\Shell\Definition\DefinitionInterface;
use ExtendsFramework\Console\Shell\Definition\Exception\OperandNotFound;
use ExtendsFramework\Console\Shell\Definition\Exception\OptionNotFound;
use ExtendsFramework\Console\Shell\Definition\Operand\OperandInterface;
use ExtendsFramework\Console\Shell\Definition\Option\OptionInterface;
use ExtendsFramework\Console\Shell\Parser\ParseResultInterface;
use PHPUnit\Framework\TestCase;

class PosixParserTest extends TestCase
{
    /**
     * Short option with combined argument.
     *
     * Test that short options with combined arguments ('-fJohn' and '-l=Doe') will be parsed.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testShortOptionWithCombinedArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturnOnConsecutiveCalls(
                'first',
                'last'
            );

        $option
            ->expects($this->exactly(2))
            ->method('isFlag')
            ->willReturn(false);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->exactly(2))
            ->method('getOption')
            ->withConsecutive(
                ['f'],
                ['l']
            )
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '-fJohn',
            '-l=Doe',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'first' => 'John',
                'last' => 'Doe',
            ], $result->getParsed());
        }
    }

    /**
     * Short option with separate argument.
     *
     * Test that short option with separate ('-n John Doe') argument will be parsed.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testShortOptionWithSeparateArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(false);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('n')
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '-n',
            'John Doe',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'name' => 'John Doe',
            ], $result->getParsed());
        }
    }

    /**
     * Short option flag.
     *
     * Test that short options ('-f', '-b' and '-b') will be parsed as flags and will contain true value. If a flag
     * exists more then once, it still must be true.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testShortOptionFlag(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(3))
            ->method('getName')
            ->willReturnOnConsecutiveCalls(
                'foo',
                'bar',
                'bar'
            );

        $option
            ->expects($this->exactly(3))
            ->method('isFlag')
            ->willReturn(true);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->exactly(3))
            ->method('getOption')
            ->withConsecutive(
                ['f'],
                ['b'],
                ['b']
            )
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '-f',
            '-b',
            '-b',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'foo' => true,
                'bar' => true,
            ], $result->getParsed());
        }
    }

    /**
     * Short option multiple flag.
     *
     * Test that short option ('-v', '-v' and '-v') will be parsed as multiple flag and will contain the amount (3) of
     * given flags as value.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testShortOptionMultipleFlag(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(3))
            ->method('getName')
            ->willReturnOnConsecutiveCalls(
                'verbose',
                'verbose',
                'verbose'
            );

        $option
            ->expects($this->exactly(3))
            ->method('isFlag')
            ->willReturn(true);

        $option
            ->expects($this->exactly(3))
            ->method('isMultiple')
            ->willReturn(true);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->exactly(3))
            ->method('getOption')
            ->withConsecutive(
                ['v'],
                ['v'],
                ['v']
            )
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '-v',
            '-v',
            '-v',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'verbose' => 3,
            ], $result->getParsed());
        }
    }

    /**
     * Required short option without argument.
     *
     * Test that an exception is thrown when the argument for a required short option is missing.
     *
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\Exception\MissingArgument::__construct()
     * @expectedException        \ExtendsFramework\Console\Shell\Parser\Posix\Exception\MissingArgument
     * @expectedExceptionMessage Short option "-f" requires an argument, non given.
     */
    public function testRequiredShortOptionWithoutArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getName')
            ->willReturn('foo');

        $option
            ->expects($this->exactly(2))
            ->method('isFlag')
            ->willReturn(false);

        $option
            ->expects($this->once())
            ->method('getShort')
            ->willReturn('f');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('f')
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $parser->parse($definition, [
            '-f',
        ]);
    }

    /**
     * Combined short options.
     *
     * Test that combined options ('-fpq') will be parsed a separate options. Options '-f' and '-b' must be true. Option
     * '-q' must contain the next argument as value ('quux').
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testCombinedShortOptions(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(3))
            ->method('getName')
            ->willReturnOnConsecutiveCalls(
                'foo',
                'bar',
                'qux'
            );

        $option
            ->expects($this->exactly(3))
            ->method('isFlag')
            ->willReturnOnConsecutiveCalls(
                true,
                true,
                false
            );

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->exactly(3))
            ->method('getOption')
            ->withConsecutive(
                ['f'],
                ['b'],
                ['q']
            )
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '-fbq',
            'quux',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'foo' => true,
                'bar' => true,
                'qux' => 'quux',
            ], $result->getParsed());
        }
    }

    /**
     * Long option with combined argument.
     *
     * Test that long option ('--name=John Doe') will be be parsed.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testLongOptionWithCombinedArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(false);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('name', true)
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '--name=John Doe',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'name' => 'John Doe',
            ], $result->getParsed());
        }
    }

    /**
     * Long option with separate argument.
     *
     * Test that long option ('--name') will be parsed and contain the next argument ('John Doe').
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testLongOptionWithSeparateArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(false);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('name', true)
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '--name',
            'John Doe',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'name' => 'John Doe',
            ], $result->getParsed());
        }
    }

    /**
     * Long option flag.
     *
     * Test that long option flag ('--force') will be parsed and will contain true.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testLongOptionFlag(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getName')
            ->willReturn('force');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(true);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('force', true)
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '--force',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'force' => true,
            ], $result->getParsed());
        }
    }

    /**
     * Long option multiple flag.
     *
     * Test that long option ('--verbose' and '--verbose') will be parsed as multiple flag and will contain the amount
     * (2) of given flags as value.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     */
    public function testLongOptionMultipleFlag(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('verbose');

        $option
            ->expects($this->exactly(2))
            ->method('isFlag')
            ->willReturn(true);

        $option
            ->expects($this->exactly(2))
            ->method('isMultiple')
            ->willReturn(true);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->exactly(2))
            ->method('getOption')
            ->with('verbose', true)
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '--verbose',
            '--verbose',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'verbose' => 2,
            ], $result->getParsed());
        }
    }

    /**
     * Long option flag with argument.
     *
     * Test that long option flag ('--name=John Doe') can not be parsed and an exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\Exception\ArgumentNotAllowed::__construct()
     * @expectedException        \ExtendsFramework\Console\Shell\Parser\Posix\Exception\ArgumentNotAllowed
     * @expectedExceptionMessage Long option argument is not allowed for flag "--name".
     */
    public function testLongOptionFlagWithArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(true);

        $option
            ->expects($this->once())
            ->method('getLong')
            ->willReturn('name');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('name', true)
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $parser->parse($definition, [
            '--name=John Doe',
        ]);
    }

    /**
     * Long option without argument.
     *
     * Test that long option ('--name') without required argument will throw an exception.
     *
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\Exception\MissingArgument::__construct()
     * @expectedException        \ExtendsFramework\Console\Shell\Parser\Posix\Exception\MissingArgument
     * @expectedExceptionMessage Long option "--name" requires an argument, non given.
     */
    public function testLongOptionWithoutArgument(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(2))
            ->method('isFlag')
            ->willReturn(false);

        $option
            ->expects($this->once())
            ->method('getLong')
            ->willReturn('name');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('name', true)
            ->willReturn($option);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $parser->parse($definition, [
            '--name',
        ]);
    }

    /**
     * Operand.
     *
     * Test that operand ('name.first') will contain the given value ('John Doe').
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOperand()
     */
    public function testOperand(): void
    {
        $operand = $this->createMock(OperandInterface::class);
        $operand
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('name.first');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOperand')
            ->with(0)
            ->willReturn($operand);

        $definition
            ->expects($this->once())
            ->method('getOperands')
            ->willReturn([
                $operand,
            ]);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            'John Doe',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'name.first' => 'John Doe',
            ], $result->getParsed());
        }
    }

    /**
     * Non strict mode.
     *
     * Test that parsing in non strict mode will only return options ('-f' and '--quite') and operand ('John Doe') from
     * definition. Other options ('-xf', '-ab' and '--help') and operand ('Jane Doe') must remain.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOperand()
     */
    public function testNonStrictMode(): void
    {
        /**
         * @var OptionNotFound  $optionNotFound
         * @var OperandNotFound $operandNotFound
         */
        $optionNotFound = $this->createMock(OptionNotFound::class);
        $operandNotFound = $this->createMock(OperandNotFound::class);

        $definition = $this->createMock(DefinitionInterface::class);

        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturnOnConsecutiveCalls(
                'force',
                'quite'
            );

        $option
            ->expects($this->exactly(2))
            ->method('isFlag')
            ->willReturn(true);

        $option
            ->expects($this->exactly(2))
            ->method('isMultiple')
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $definition
            ->expects($this->exactly(5))
            ->method('getOption')
            ->withConsecutive(
                ['x'],
                ['f'],
                ['a'],
                ['help', true],
                ['quite', true]
            )
            ->willReturnOnConsecutiveCalls(
                $this->throwException($optionNotFound),
                $option,
                $this->throwException($optionNotFound),
                $this->throwException($optionNotFound),
                $option
            );

        $operand = $this->createMock(OperandInterface::class);
        $operand
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name');

        $definition
            ->expects($this->exactly(2))
            ->method('getOperand')
            ->withConsecutive(
                [0],
                [1]
            )
            ->willReturnOnConsecutiveCalls(
                $operand,
                $this->throwException($operandNotFound)
            );

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '-xf',
            '-fab',
            'John Doe',
            '--help',
            '--quite',
            'Jane Doe',
        ], false);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'force' => 1,
                'name' => 'John Doe',
                'quite' => true,
            ], $result->getParsed());

            $this->assertSame([
                '-xf',
                '-ab',
                '--help',
                'Jane Doe',
            ], $result->getRemaining());
        }
    }

    /**
     * Missing operand.
     *
     * Test that missing operand can not be parsed and an exception will be thrown.
     *
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOperand()
     * @covers                   \ExtendsFramework\Console\Shell\Parser\Posix\Exception\MissingOperand::__construct()
     * @expectedException        \ExtendsFramework\Console\Shell\Parser\Posix\Exception\MissingOperand
     * @expectedExceptionMessage Operand "name.first" is required.
     */
    public function testMissingOperand(): void
    {
        $operand = $this->createMock(OperandInterface::class);
        $operand
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name.first');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOperands')
            ->willReturn([
                $operand,
            ]);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $parser->parse($definition, []);
    }

    /**
     * Unknown operand.
     *
     * Test that an unknown operand can not be parsed and an exception will be thrown.
     *
     * @covers            \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers            \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers            \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOperand()
     * @expectedException \ExtendsFramework\Console\Shell\Definition\Exception\OperandNotFound
     */
    public function testUnknownOperand(): void
    {
        /**
         * @var OperandNotFound $exception
         */
        $exception = $this->createMock(OperandNotFound::class);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOperand')
            ->with(0)
            ->willThrowException($exception);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $parser->parse($definition, [
            'John Doe',
        ]);
    }

    /**
     * Unknown option.
     *
     * Test that an unknown option can not be parsed and will throw an exception.
     *
     * @covers            \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers            \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers            \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOption()
     * @expectedException \ExtendsFramework\Console\Shell\Definition\Exception\OptionNotFound
     */
    public function testUnknownOption(): void
    {
        /**
         * @var OptionNotFound $exception
         */
        $exception = $this->createMock(OptionNotFound::class);

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOption')
            ->with('x')
            ->willThrowException($exception);

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $parser->parse($definition, [
            '-x',
        ]);
    }

    /**
     * Terminator.
     *
     * Test that the terminator ('--') will terminate further parsing and return everything as an operand.
     *
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parse()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::parseArguments()
     * @covers \ExtendsFramework\Console\Shell\Parser\Posix\PosixParser::getOperand()
     */
    public function testTerminator(): void
    {
        $operand1 = $this->createMock(OperandInterface::class);
        $operand1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('first');

        $operand2 = $this->createMock(OperandInterface::class);
        $operand2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('last');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->exactly(2))
            ->method('getOperand')
            ->withConsecutive(
                [0],
                [1]
            )
            ->willReturnOnConsecutiveCalls(
                $operand1,
                $operand2
            );

        /**
         * @var DefinitionInterface $definition
         */
        $parser = new PosixParser();
        $result = $parser->parse($definition, [
            '--',
            '-John',
            '--Doe',
        ]);

        $this->assertInstanceOf(ParseResultInterface::class, $result);
        if ($result instanceof ParseResultInterface) {
            $this->assertSame([
                'first' => '-John',
                'last' => '--Doe',
            ], $result->getParsed());
        }
    }
}
