<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Suggester\SimilarText;

use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Suggester\SuggesterInterface;

class SimilarTextSuggester implements SuggesterInterface
{
    /**
     * Lowest percentage to match.
     *
     * @var int|null
     */
    private $percentage;

    /**
     * Create new suggester.
     *
     * @param int $percentage
     */
    public function __construct(int $percentage = null)
    {
        $this->percentage = $percentage;
    }

    /**
     * @inheritDoc
     */
    public function suggest(string $phrase, CommandInterface ...$commands): ?CommandInterface
    {
        $closest = null;
        $shortest = 0;

        foreach ($commands as $command) {
            similar_text($phrase, $command->getName(), $percentage);
            if ($percentage >= $this->getPercentage()) {
                if ($percentage === 100.0) {
                    return $command;
                }

                if ($percentage > $shortest) {
                    $closest = $command;
                    $shortest = $percentage;
                }
            }
        }

        return $closest;
    }

    /**
     * Get percentage.
     *
     * @return int
     */
    private function getPercentage(): int
    {
        return $this->percentage ?? 60;
    }
}
