<?php

declare(strict_types=1);

namespace PHPMND\Visitor;

use PHPMND\HintList;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class HintVisitor extends NodeVisitorAbstract
{
    /**
     * @var HintList
     */
    private $hintList;

    public function __construct(HintList $hintList)
    {
        $this->hintList = $hintList;
    }

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Const_) {
            if ($node->value instanceof Scalar === false) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            $constantValue = $node->value->value;
            $constParent = $node->getAttribute('parent');

            if ($constParent instanceof ClassConst) {
                $classConstParent = $constParent->getAttribute('parent');

                if ($classConstParent instanceof Class_) {
                    $this->hintList->addClassCont($constantValue, (string) $classConstParent->name, (string) $node->name);
                }
            }
        }

        return null;
    }
}
