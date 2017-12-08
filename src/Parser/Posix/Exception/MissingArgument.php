<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Parser\Posix\Exception;

use Exception;
use ExtendsFramework\Console\Shell\Definition\Option\OptionInterface;
use ExtendsFramework\Console\Shell\Parser\ParserException;

class MissingArgument extends Exception implements ParserException
{
    /**
     * Required option has no argument.
     *
     * @param OptionInterface $option
     * @param bool|null       $long
     */
    public function __construct(OptionInterface $option, bool $long = null)
    {
        parent::__construct(sprintf(
            '%s option "%s" requires an argument, non given.',
            $long ? 'Long' : 'Short',
            $long ? '--' . $option->getLong() : '-' . $option->getShort()
        ));
    }
}
