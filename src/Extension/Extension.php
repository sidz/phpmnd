<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PHPMND\Console\Option;
use PhpParser\Node;

abstract class Extension
{
    /**
     * @var Option
     */
    protected $option;

    abstract public function extend(Node $node): bool;

    abstract public function getName(): string;

    public function setOption(Option $option): void
    {
        $this->option = $option;
    }
}
