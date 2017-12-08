<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell;

use ExtendsFramework\Console\Shell\Descriptor\DescriptorInterface;
use ExtendsFramework\Console\Shell\Parser\ParserInterface;
use ExtendsFramework\Console\Shell\Suggester\SuggesterInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

class ShellBuilderTest extends TestCase
{
    /**
     * Build.
     *
     * Test that builder will build and return a shell.
     *
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::setName()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::setProgram()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::setVersion()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::setDescriptor()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::setParser()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::setSuggester()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::addCommand()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getName()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getProgram()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getVersion()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getDescriptor()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getParser()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getSuggester()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::getCommands()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::build()
     * @covers \ExtendsFramework\Console\Shell\ShellBuilder::reset()
     */
    public function testBuild(): void
    {
        $suggester = $this->createMock(SuggesterInterface::class);
        $descriptor = $this->createMock(DescriptorInterface::class);
        $parser = $this->createMock(ParserInterface::class);

        /**
         * @var SuggesterInterface  $suggester
         * @var DescriptorInterface $descriptor
         * @var ParserInterface     $parser
         */
        $builder = new ShellBuilder();
        $shell = $builder
            ->setName('Acme console')
            ->setProgram('acme')
            ->setVersion('1.0')
            ->setDescriptor($descriptor)
            ->setParser($parser)
            ->setSuggester($suggester)
            ->addCommand('do.task', 'Do some fancy task!', [
                [
                    'name' => 'first_name',
                ],
            ], [
                [
                    'name' => 'force',
                    'description' => 'Force things!',
                    'short' => 'f',
                ],
            ], [
                'task' => stdClass::class,
            ])
            ->build();

        $this->assertInstanceOf(ShellInterface::class, $shell);
    }
}
