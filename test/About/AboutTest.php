<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\About;

use PHPUnit\Framework\TestCase;

class AboutTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will correct values.
     *
     * @covers \ExtendsFramework\Console\Shell\About\About::__construct()
     * @covers \ExtendsFramework\Console\Shell\About\About::getName()
     * @covers \ExtendsFramework\Console\Shell\About\About::getProgram()
     * @covers \ExtendsFramework\Console\Shell\About\About::getVersion()
     */
    public function testGetMethods(): void
    {
        $about = new About('Extends Framework Console', 'extends', '0.1');

        $this->assertSame('Extends Framework Console', $about->getName());
        $this->assertSame('extends', $about->getProgram());
        $this->assertSame('0.1', $about->getVersion());
    }
}
