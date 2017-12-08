<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\About;

class About implements AboutInterface
{
    /**
     * Shell name.
     *
     * @var string
     */
    protected $name;

    /**
     * Program to run shel..
     *
     * @var string
     */
    protected $program;

    /**
     * Shell version.
     *
     * @var string
     */
    protected $version;

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
