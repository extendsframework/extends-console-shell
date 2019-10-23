<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Definition\Exception;

use Exception;
use ExtendsFramework\Shell\Definition\DefinitionException;

class OptionNotFound extends Exception implements DefinitionException
{
    /**
     * No option with $name.
     *
     * @param string    $name
     * @param bool|null $long
     */
    public function __construct(string $name, bool $long = null)
    {
        parent::__construct(sprintf(
            'No %s option found for name "%s%s".',
            $long ? 'long' : 'short',
            $long ? '--' : '-',
            $name
        ));
    }
}
