<?php

declare(strict_types=1);

namespace PHPMND\Visitor;

use function in_array;
use function is_numeric;
use PHPMND\Console\Option;
use PHPMND\FileReport;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\UnaryPlus;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class DetectorVisitor extends NodeVisitorAbstract
{
    /**
     * @var FileReport
     */
    private $fileReport;

    /**
     * @var Option
     */
    private $option;

    public function __construct(FileReport $fileReport, Option $option)
    {
        $this->fileReport = $fileReport;
        $this->option = $option;
    }

    public function enterNode(Node $node): ?int
    {
        if ($this->isIgnoreableConst($node)) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        /** @var LNumber|DNumber|String_ $scalar */
        $scalar = $node;

        if ($this->hasSign($node)) {
            $node = $node->getAttribute('parent');

            if ($this->isMinus($node)) {
                if (!isset($scalar->value)) {
                    return null;
                }
                $scalar->value = -$scalar->value;
            }
        }

        if ($this->isNumber($scalar) || $this->isString($scalar)) {
            foreach ($this->option->getExtensions() as $extension) {
                $extension->setOption($this->option);

                if ($extension->extend($node)) {
                    $this->fileReport->addEntry($scalar->getLine(), $scalar->value);

                    return null;
                }
            }
        }

        return null;
    }

    private function isIgnoreableConst(Node $node): bool
    {
        return $node instanceof Const_ &&
            ($this->isNumber($node->value) || $this->isString($node->value));
    }

    private function isNumber(Node $node): bool
    {
        $isNumber = (
            $node instanceof LNumber ||
            $node instanceof DNumber ||
            $this->isValidNumeric($node)
        );

        return $isNumber && $this->ignoreNumber($node) === false;
    }

    private function isString(Node $node): bool
    {
        return $this->option->includeStrings() && $node instanceof String_ && $this->ignoreString($node) === false;
    }

    private function ignoreNumber(Node $node): bool
    {
        return in_array($node->value, $this->option->getIgnoreNumbers(), true);
    }

    private function ignoreString(Node $node): bool
    {
        return in_array($node->value, $this->option->getIgnoreStrings(), true);
    }

    private function hasSign(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof UnaryMinus
            || $node->getAttribute('parent') instanceof UnaryPlus;
    }

    private function isMinus(Node $node): bool
    {
        return $node instanceof UnaryMinus;
    }

    private function isValidNumeric(Node $node): bool
    {
        return $this->option->includeNumericStrings() &&
        isset($node->value) &&
        is_numeric($node->value) &&
        $this->ignoreString($node) === false;
    }
}
