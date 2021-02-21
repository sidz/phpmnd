<?php

declare(strict_types=1);

namespace PHPMND;

use Symfony\Component\Finder\SplFileInfo;

class FileReport
{
    /**
     * @var array
     */
    private $entries = [];

    /**
     * @var SplFileInfo
     */
    private $file;

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    /**
     * @param int|float $value
     */
    public function addEntry(int $line, $value): void
    {
        $this->entries[] = [
            'line' => $line,
            'value' => $value,
        ];
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function hasMagicNumbers(): bool
    {
        return empty($this->entries) === false;
    }
}
