<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Parser;

use ExtendsFramework\Console\Shell\Definition\DefinitionException;
use ExtendsFramework\Console\Shell\Definition\DefinitionInterface;

interface ParserInterface
{
    /**
     * Parse $arguments against $definition.
     *
     * When $strict mode is disabled, only operands and options that can be matched will be returned, no exception
     * will be thrown. Arguments that can not be parsed will be added to $remaining for later usage.
     *
     * @param DefinitionInterface $definition
     * @param array               $arguments
     * @param bool|null           $strict
     * @return ParseResultInterface
     * @throws ParserException
     * @throws DefinitionException
     */
    public function parse(DefinitionInterface $definition, array $arguments, bool $strict = null): ParseResultInterface;
}
