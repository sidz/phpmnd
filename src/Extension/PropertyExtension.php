<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;

class PropertyExtension extends Extension
{
    public function getName(): string
    {
        return 'property';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof PropertyProperty;
    }
}
