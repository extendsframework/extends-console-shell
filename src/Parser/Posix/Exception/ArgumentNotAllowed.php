<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Parser\Posix\Exception;

use Exception;
use ExtendsFramework\Shell\Definition\Option\OptionInterface;
use ExtendsFramework\Shell\Parser\ParserException;

class ArgumentNotAllowed extends Exception implements ParserException
{
    /**
     * Flag option has an argument.
     *
     * @param OptionInterface $option
     * @param bool            $long
     */
    public function __construct(OptionInterface $option, bool $long = null)
    {
        parent::__construct(sprintf(
            '%s option argument is not allowed for flag "%s".',
            $long ? 'Long' : 'Short',
            $long ? '--' . $option->getLong() : '-' . $option->getShort()
        ));
    }
}
