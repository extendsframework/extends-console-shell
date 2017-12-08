<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Definition\Option\Exception;

use Exception;
use ExtendsFramework\Console\Shell\Definition\Option\OptionException;

class NoShortAndLongName extends Exception implements OptionException
{
    /**
     * Both short and long names are missing.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf(
            'Option "%s" requires at least a short or long name, both not given.',
            $name
        ));
    }
}
