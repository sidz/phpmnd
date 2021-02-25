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
     * @var array
     */
    private $stack;

    public function beforeTraverse(array $nodes): void
    {
        $this->stack = [];
    }

    public function enterNode(Node $node): void
    {
        if (empty($this->stack) === false) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    public function leaveNode(Node $node): void
    {
        array_pop($this->stack);
    }
}
