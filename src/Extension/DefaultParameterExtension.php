<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\Node\Param;

class DefaultParameterExtension extends Extension
{
    public function getName(): string
    {
        return 'default_parameter';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Param;
    }
}
