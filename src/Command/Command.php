<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Command;

use ExtendsFramework\Shell\Definition\DefinitionInterface;

class Command implements CommandInterface
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $name;

    /**
     * Command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Command definition.
     *
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * Extra command parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Create new command for $name with $description and $definition.
     *
     * @param string              $name
     * @param string              $description
     * @param DefinitionInterface $definition
     * @param array|null          $parameters
     */
    public function __construct(
        string $name,
        string $description,
        DefinitionInterface $definition,
        array $parameters = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->definition = $definition;
        $this->parameters = $parameters ?: [];
    }

    /**
     * /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getDefinition(): DefinitionInterface
    {
        return $this->definition;
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
