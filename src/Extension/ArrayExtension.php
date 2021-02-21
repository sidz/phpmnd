<?php

declare(strict_types=1);

namespace PHPMND\Extension;

use function is_numeric;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;

class ArrayExtension extends Extension
{
    public function getName(): string
    {
        return 'array';
    }

    public function extend(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        return (
            $parent instanceof ArrayItem &&
            $this->ignoreArray($parent) === false
          ) || $parent instanceof ArrayDimFetch;
    }

    private function ignoreArray(ArrayItem $node): bool
    {
        $arrayKey = $node->key;

        return $this->option->allowArrayMapping() &&
        $arrayKey instanceof String_ &&
        false === ($this->option->includeNumericStrings() && is_numeric($arrayKey->value));
    }
}
