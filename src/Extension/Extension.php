<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PhpParser\Node;
use PHPMND\Console\Option;

abstract class Extension
{
    /**
     * @var Option
     */
    protected $option;

    abstract public function extend(Node $node): bool;

    abstract public function getName(): string;

    public function setOption(Option $option)
    {
        $this->option = $option;
    }
}
