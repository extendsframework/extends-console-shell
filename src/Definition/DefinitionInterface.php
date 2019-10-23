<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition;

use ExtendsFramework\Shell\Definition\Operand\OperandInterface;
use ExtendsFramework\Shell\Definition\Option\OptionInterface;

interface DefinitionInterface
{
    /**
     * Get all options.
     *
     * @return OptionInterface[]
     */
    public function getOptions(): array;

    /**
     * Get all operands.
     *
     * @return OperandInterface[]
     */
    public function getOperands(): array;

    /**
     * Get option for $name.
     *
     * @param string    $name
     * @param bool|null $long
     * @return OptionInterface
     * @throws DefinitionException When option with short $name is not found.
     */
    public function getOption(string $name, bool $long = null): OptionInterface;

    /**
     * Get operand for $position.
     *
     * @param int $position
     * @return OperandInterface
     * @throws DefinitionException When operand at $position is not found.
     */
    public function getOperand(int $position): OperandInterface;
}
