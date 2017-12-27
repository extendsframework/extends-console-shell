<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Definition\Option;

use ExtendsFramework\Console\Shell\Definition\Option\Exception\NoShortAndLongName;

class Option implements OptionInterface
{
    /**
     * Option name.
     *
     * @var string
     */
    protected $name;

    /**
     * Option description.
     *
     * @var string
     */
    protected $description;

    /**
     * Short name.
     *
     * @var null|string
     */
    protected $short;

    /**
     * Long name.
     *
     * @var null|string
     */
    protected $long;

    /**
     * If a argument is allowed.
     *
     * @var bool
     */
    protected $isFlag;

    /**
     * If multiple arguments are allowed.
     *
     * @var bool
     */
    protected $isMultiple;

    /**
     * Create new option.
     *
     * @param string      $name
     * @param string      $description
     * @param string|null $short
     * @param string|null $long
     * @param bool|null   $isFlag
     * @param bool|null   $isMultiple
     * @throws NoShortAndLongName
     */
    public function __construct(
        string $name,
        string $description,
        string $short = null,
        string $long = null,
        bool $isFlag = null,
        bool $isMultiple = null
    ) {
        if ($short === null && $long === null) {
            throw new NoShortAndLongName($name);
        }

        $this->name = $name;
        $this->description = $description;
        $this->short = $short;
        $this->long = $long;
        $this->isFlag = $isFlag ?? true;
        $this->isMultiple = $isMultiple ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getShort(): ?string
    {
        return $this->short;
    }

    /**
     * @inheritDoc
     */
    public function getLong(): ?string
    {
        return $this->long;
    }

    /**
     * @inheritDoc
     */
    public function isFlag(): bool
    {
        return $this->isFlag;
    }

    /**
     * @inheritDoc
     */
    public function isMultiple(): bool
    {
        return $this->isMultiple;
    }
}
