<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use function in_array;
use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

class ArgumentExtension extends Extension
{
    public function getName(): string
    {
        return 'argument';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Arg && $this->ignoreFunc($node) === false;
    }

    private function ignoreFunc(Node $node): bool
    {
        /** @var Node|null $funcCallNode */
        $parentNode = ParentConnector::findParent($node);

        if ($parentNode === null) {
            return false;
        }

        /** @var Node|null $funcCallNode */
        $funcCallNode = ParentConnector::findParent($parentNode);

        return
            $funcCallNode instanceof FuncCall
            &&
            $funcCallNode->name instanceof Name
            &&
            in_array($funcCallNode->name->getLast(), $this->option->getIgnoreFuncs(), true);
    }
}
