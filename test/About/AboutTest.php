<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\About;

use PHPUnit\Framework\TestCase;

class AboutTest extends TestCase
{
    /**
     * Get methods.
     *
     * Test that get methods will correct values.
     *
     * @covers \ExtendsFramework\Shell\About\About::__construct()
     * @covers \ExtendsFramework\Shell\About\About::getName()
     * @covers \ExtendsFramework\Shell\About\About::getProgram()
     * @covers \ExtendsFramework\Shell\About\About::getVersion()
     */
    public function testGetMethods(): void
    {
        $about = new About('Extends Framework Console', 'extends', '0.1');

        $this->assertSame('Extends Framework Console', $about->getName());
        $this->assertSame('extends', $about->getProgram());
        $this->assertSame('0.1', $about->getVersion());
    }
}
