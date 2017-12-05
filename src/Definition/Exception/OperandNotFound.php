<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition\Exception;

use Exception;
use ExtendsFramework\Shell\Definition\DefinitionException;

class OperandNotFound extends Exception implements DefinitionException
{
    /**
     * No operand for $position.
     *
     * @param int $position
     */
    public function __construct(int $position)
    {
        parent::__construct(sprintf(
            'No operand found for position "%s".',
            $position
        ));
    }
}
