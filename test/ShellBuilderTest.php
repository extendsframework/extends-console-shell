<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell;

use ExtendsFramework\Shell\Descriptor\DescriptorInterface;
use ExtendsFramework\Shell\Parser\ParserInterface;
use ExtendsFramework\Shell\Suggester\SuggesterInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

class ShellBuilderTest extends TestCase
{
    /**
     * Build.
     *
     * Test that builder will build and return a shell.
     *
     * @covers \ExtendsFramework\Shell\ShellBuilder::setName()
     * @covers \ExtendsFramework\Shell\ShellBuilder::setProgram()
     * @covers \ExtendsFramework\Shell\ShellBuilder::setVersion()
     * @covers \ExtendsFramework\Shell\ShellBuilder::setDescriptor()
     * @covers \ExtendsFramework\Shell\ShellBuilder::setParser()
     * @covers \ExtendsFramework\Shell\ShellBuilder::setSuggester()
     * @covers \ExtendsFramework\Shell\ShellBuilder::addCommand()
     * @covers \ExtendsFramework\Shell\ShellBuilder::build()
     * @covers \ExtendsFramework\Shell\ShellBuilder::reset()
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

        $this->assertIsObject($shell);
    }
}
