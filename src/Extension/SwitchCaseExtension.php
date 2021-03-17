<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Case_;

class SwitchCaseExtension extends Extension
{
    public function getName(): string
    {
        return 'switch_case';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Case_;
    }
}
