<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\About;

class About implements AboutInterface
{
    /**
     * Shell name.
     *
     * @var string
     */
    private $name;

    /**
     * Program to run shel..
     *
     * @var string
     */
    private $program;

    /**
     * Shell version.
     *
     * @var string
     */
    private $version;

    /**
     * About constructor.
     *
     * @param string $name
     * @param string $program
     * @param string $version
     */
    public function __construct(string $name, string $program, string $version)
    {
        $this->name = $name;
        $this->program = $program;
        $this->version = $version;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getProgram(): string
    {
        return $this->program;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
