<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Suggester;

use ExtendsFramework\Console\Shell\Command\CommandInterface;

interface SuggesterInterface
{
    /**
     * Find the best matching command in $commands to suggest for $phrase.
     *
     * @param string             $phrase
     * @param CommandInterface[] ...$commands
     * @return CommandInterface|null
     */
    public function suggest(string $phrase, CommandInterface ...$commands): ?CommandInterface;
}
