<?php

declare(strict_types=1);

namespace PHPMND;

use function sprintf;

class HintList
{
    /**
     * @var array
     */
    private $constants = [];

    public function getHintsByValue($magicNumber): array
    {
        $hints = [];

        foreach ($this->constants as $constant) {
            if ($constant['value'] === $magicNumber) {
                $hints[] = $constant['hint'];
            }
        }

        return $hints;
    }

    public function hasHints(): bool
    {
        return empty($this->constants) === false;
    }

    public function addClassCont($value, string $className, string $constName): void
    {
        $this->constants[] = [
            'value' => $value,
            'hint' => sprintf('%s::%s', $className, $constName),
        ];
    }
}
