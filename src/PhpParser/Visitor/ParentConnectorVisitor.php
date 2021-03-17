<?php

declare(strict_types=1);

namespace PHPMND\PhpParser\Visitor;

use function array_pop;
use function count;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ParentConnectorVisitor extends NodeVisitorAbstract
{
    /**
     * @var Node[]
     */
    private $stack;

    public function beforeTraverse(array $nodes): void
    {
        $this->stack = [];
    }

    public function enterNode(Node $node): void
    {
        $stackCount = count($this->stack);

        ParentConnector::setParent($node, $this->stack[$stackCount - 1] ?? null);

        $this->stack[] = $node;
    }

    public function leaveNode(Node $node): void
    {
        array_pop($this->stack);
    }
}
