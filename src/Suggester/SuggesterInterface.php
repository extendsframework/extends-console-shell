<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Suggester;

use ExtendsFramework\Shell\Command\CommandInterface;

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
