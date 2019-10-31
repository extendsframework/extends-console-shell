<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Descriptor;

use Exception;
use ExtendsFramework\Console\Output\OutputInterface;
use ExtendsFramework\Shell\About\AboutInterface;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Definition\DefinitionInterface;
use ExtendsFramework\Shell\Definition\Operand\OperandInterface;
use ExtendsFramework\Shell\Definition\Option\OptionInterface;
use PHPUnit\Framework\TestCase;
use Throwable;

class DescriptorTest extends TestCase
{
    /**
     * Shell short.
     *
     * Test that descriptor can describe shell (short).
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::shell()
     */
    public function testShellShort(): void
    {
        $output = new OutputStub();

        $definition = $this->createMock(DefinitionInterface::class);

        $about = $this->createMock(AboutInterface::class);
        $about
            ->method('getName')
            ->willReturn('Extends Framework Console');

        $about
            ->method('getProgram')
            ->willReturn('extends');

        $about
            ->method('getVersion')
            ->willReturn('0.1');

        /**
         * @var DefinitionInterface $definition
         * @var AboutInterface      $about
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->shell($about, $definition, [], true);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            '',
            'See \'extends --help\' for more information about available commands and options.',
        ], $output->getBuffer());
    }

    /**
     * Shell long.
     *
     * Test that descriptor can describe shell (long).
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::shell()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::getOptionNotation()
     */
    public function testShellLong(): void
    {
        $output = new OutputStub();

        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getShort')
            ->willReturn('h');

        $option
            ->expects($this->once())
            ->method('getLong')
            ->willReturn('help');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(false);

        $option
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Show help.');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([
                $option,
            ]);

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $command
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Do some fancy task!');

        $about = $this->createMock(AboutInterface::class);
        $about
            ->method('getName')
            ->willReturn('Extends Framework Console');

        $about
            ->method('getProgram')
            ->willReturn('extends');

        $about
            ->method('getVersion')
            ->willReturn('0.1');

        /**
         * @var DefinitionInterface $definition
         * @var AboutInterface      $about
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->shell($about, $definition, [
            $command,
        ]);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => 'Extends Framework Console (version 0.1)',
            1 => '',
            2 => 'Usage:',
            3 => '',
            4 => 'extends <command> [<arguments>] [<options>]',
            5 => '',
            6 => 'Commands:',
            7 => '',
            8 => 'do.taskDo some fancy task!',
            9 => '',
            10 => 'Options:',
            11 => '',
            12 => '-h=|--help=Show help.',
            13 => '',
            14 => 'See \'extends <command> --help\' for more information about a command.',
        ], $output->getBuffer());
    }

    /**
     * Shell long without commands.
     *
     * Test that descriptor can describe shell (long) and will show a dash when no commands are defined.
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::shell()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::getOptionNotation()
     */
    public function testShellLongWithoutCommands(): void
    {
        $output = new OutputStub();

        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getShort')
            ->willReturn('h');

        $option
            ->expects($this->once())
            ->method('getLong')
            ->willReturn('help');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(false);

        $option
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Show help.');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([
                $option,
            ]);

        $about = $this->createMock(AboutInterface::class);
        $about
            ->method('getName')
            ->willReturn('Extends Framework Console');

        $about
            ->method('getProgram')
            ->willReturn('extends');

        $about
            ->method('getVersion')
            ->willReturn('0.1');

        /**
         * @var DefinitionInterface $definition
         * @var AboutInterface      $about
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->shell($about, $definition, []);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => 'Extends Framework Console (version 0.1)',
            1 => '',
            2 => 'Usage:',
            3 => '',
            4 => 'extends <command> [<arguments>] [<options>]',
            5 => '',
            6 => 'Commands:',
            7 => '',
            8 => 'No commands defined.',
            9 => '',
            10 => 'Options:',
            11 => '',
            12 => '-h=|--help=Show help.',
            13 => '',
            14 => 'See \'extends <command> --help\' for more information about a command.',
        ], $output->getBuffer());
    }

    /**
     * Command short.
     *
     * Test that descriptor can describe command (short).
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::command()
     */
    public function testCommandShort(): void
    {
        $output = new OutputStub();

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $about = $this->createMock(AboutInterface::class);
        $about
            ->method('getName')
            ->willReturn('Extends Framework Console');

        $about
            ->method('getProgram')
            ->willReturn('extends');

        $about
            ->method('getVersion')
            ->willReturn('0.1');

        /**
         * @var CommandInterface $command
         * @var AboutInterface   $about
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->command($about, $command, true);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => '',
            1 => 'See \'extends do.task --help\' for more information about the command.',
        ], $output->getBuffer());
    }

    /**
     * Command long.
     *
     * Test that descriptor can describe command (long).
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::command()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::getOptionNotation()
     */
    public function testCommandLong(): void
    {
        $output = new OutputStub();

        $operand = $this->createMock(OperandInterface::class);
        $operand
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name');

        $option = $this->createMock(OptionInterface::class);
        $option
            ->expects($this->once())
            ->method('getShort')
            ->willReturn('o');

        $option
            ->expects($this->once())
            ->method('getLong')
            ->willReturn('option');

        $option
            ->expects($this->once())
            ->method('isFlag')
            ->willReturn(true);

        $option
            ->expects($this->once())
            ->method('isMultiple')
            ->willReturn(true);

        $option
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Show option.');

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([
                $option,
            ]);

        $definition
            ->expects($this->once())
            ->method('getOperands')
            ->willReturn([
                $operand,
            ]);

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $command
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $about = $this->createMock(AboutInterface::class);
        $about
            ->method('getName')
            ->willReturn('Extends Framework Console');

        $about
            ->method('getProgram')
            ->willReturn('extends');

        $about
            ->method('getVersion')
            ->willReturn('0.1');

        /**
         * @var CommandInterface $command
         * @var AboutInterface   $about
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->command($about, $command);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => 'Extends Framework Console (version 0.1)',
            1 => '',
            2 => 'Usage:',
            3 => '',
            4 => 'extends do.task <name> [<options>] ',
            5 => '',
            6 => 'Options:',
            7 => '',
            8 => '-o+|--option+Show option.',
            9 => '',
            10 => 'See \'extends --help\' for more information about this shell and default options.',
        ], $output->getBuffer());
    }

    /**
     * Command long without operands and options.
     *
     * Test that descriptor can describe command (long) without operands and options.
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::command()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::getOptionNotation()
     */
    public function testCommandLongWithoutOperandsAndOptions(): void
    {
        $output = new OutputStub();

        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);

        $definition
            ->expects($this->once())
            ->method('getOperands')
            ->willReturn([]);

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        $command
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $about = $this->createMock(AboutInterface::class);
        $about
            ->method('getName')
            ->willReturn('Extends Framework Console');

        $about
            ->method('getProgram')
            ->willReturn('extends');

        $about
            ->method('getVersion')
            ->willReturn('0.1');

        /**
         * @var CommandInterface $command
         * @var AboutInterface   $about
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->command($about, $command);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => 'Extends Framework Console (version 0.1)',
            1 => '',
            2 => 'Usage:',
            3 => '',
            4 => 'extends do.task ',
            5 => '',
            6 => 'See \'extends --help\' for more information about this shell and default options.',
        ], $output->getBuffer());
    }

    /**
     * Suggest.
     *
     * Test that descriptor can suggest.
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::suggest()
     */
    public function testSuggest(): void
    {
        $output = new OutputStub();

        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('getName')
            ->willReturn('do.task');

        /**
         * @var OutputInterface  $output
         * @var CommandInterface $command
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->suggest($command);

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => '',
            1 => 'Did you mean "do.task"?',
        ], $output->getBuffer());
    }

    /**
     * Exception.
     *
     * Test that descriptor can describe exception.
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::exception()
     */
    public function testException(): void
    {
        $output = new OutputStub();

        /**
         * @var OutputInterface $output
         * @var Throwable       $exception
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->exception(new Exception('Random exception message!'));

        $this->assertSame($descriptor, $instance);
        $this->assertSame([
            0 => 'Random exception message!',
        ], $output->getBuffer());
    }

    /**
     * Verbosity.
     *
     * Set verbosity for output to 3.
     *
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::__construct()
     * @covers \ExtendsFramework\Shell\Descriptor\Descriptor::setVerbosity()
     */
    public function testVerbosity(): void
    {
        $output = $this->createMock(OutputInterface::class);
        $output
            ->expects($this->once())
            ->method('setVerbosity')
            ->with(3)
            ->willReturnSelf();

        /**
         * @var OutputInterface     $output
         * @var DefinitionInterface $definition
         */
        $descriptor = new Descriptor($output);
        $instance = $descriptor->setVerbosity(3);

        $this->assertSame($descriptor, $instance);
    }
}
