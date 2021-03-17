<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;

class AssignExtension extends Extension
{
    public function getName(): string
    {
        return 'assign';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Assign;
    }
}
