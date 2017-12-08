<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Exception;

use Exception;
use ExtendsFramework\Console\Shell\ShellException;

class CommandNotFound extends Exception implements ShellException
{
    /**
     * Command not found for $name.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf(
            'Command "%s" not found.',
            $name
        ));
    }
}
