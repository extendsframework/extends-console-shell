<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Descriptor;

use ExtendsFramework\Console\Formatter\FormatterInterface;
use ExtendsFramework\Console\Output\OutputInterface;
use PHPUnit\Framework\TestCase;

class OutputStub extends TestCase implements OutputInterface
{
    /**
     * Buffer with output strings.
     *
     * @var array
     */
    protected $output = [];

    /**
     * Output line index.
     *
     * @var int
     */
    protected $index = 0;

    /**
     * @inheritDoc
     */
    public function text(string $text, FormatterInterface $formatter = null, int $verbosity = null): OutputInterface
    {
        if (array_key_exists($this->index, $this->output) === false) {
            $this->output[$this->index] = '';
        }

        $this->output[$this->index] .= $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function line(string $text, FormatterInterface $formatter = null, int $verbosity = null): OutputInterface
    {
        $this->text($text);
        $this->index++;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function newLine(int $verbosity = null): OutputInterface
    {
        $this->output[] = '';
        $this->index++;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clear(): OutputInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): int
    {
        return 80;
    }

    /**
     * @inheritDoc
     */
    public function getLines(): int
    {
        return 120;
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): FormatterInterface
    {
        $formatter = $this->createMock(FormatterInterface::class);
        $formatter
            ->method($this->anything())
            ->willReturnSelf();

        $formatter
            ->method('create')
            ->willReturnCallback(static function (string $text) {
                return $text;
            });

        /** @var FormatterInterface $formatter */
        return $formatter;
    }

    /**
     * @inheritDoc
     */
    public function setVerbosity(int $verbosity): OutputInterface
    {
        return $this;
    }

    /**
     * Return buffered output.
     *
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->output;
    }
}
