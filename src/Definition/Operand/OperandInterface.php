<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Definition\Operand;

interface OperandInterface
{
    /**
     * Get operand name.
     *
     * @return string
     */
    public function getName(): string;
}
