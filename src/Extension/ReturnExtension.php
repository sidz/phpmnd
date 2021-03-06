<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;

class ReturnExtension extends Extension
{
    public function getName(): string
    {
        return 'return';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Return_;
    }
}
