<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition\Operand;

class Operand implements OperandInterface
{
    /**
     * Operand name.
     *
     * @var string
     */
    private $name;

    /**
     * Create new OperandInterface with $name.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
}
