<?php

declare(strict_types=1);

namespace PHPMND\Tests\PhpParser\Visitor;

use InvalidArgumentException;
use PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node\Stmt\Nop;
use PHPUnit\Framework\TestCase;

class ParentConnectorTest extends TestCase
{
    public function test_it_can_provide_the_node_parent(): void
    {
        $parent = new Nop();

        $node = new Nop(['parent' => $parent]);

        $this->assertSame($parent, ParentConnector::getParent($node));
        $this->assertSame($parent, ParentConnector::findParent($node));
    }

    public function test_it_can_look_for_the_node_parent(): void
    {
        $parent = new Nop();

        $node1 = new Nop(['parent' => $parent]);
        $node2 = new Nop(['parent' => null]);
        $node3 = new Nop();

        $this->assertSame($parent, ParentConnector::findParent($node1));
        $this->assertNull(ParentConnector::findParent($node2));
        $this->assertNull(ParentConnector::findParent($node3));
    }

    public function test_it_cannot_provide_the_node_parent_if_has_not_be_set_yet(): void
    {
        $node = new Nop();

        $this->expectException(InvalidArgumentException::class);

        // We are not interested in a more helpful message here since it would be the result of
        // a misconfiguration on our part rather than a user one. Plus this would require some
        // extra processing on a part which is quite a hot path.

        ParentConnector::getParent($node);
    }

    public function test_it_can_set_a_node_parent(): void
    {
        $parent = new Nop();
        $node = new Nop();

        ParentConnector::setParent($node, $parent);

        $this->assertSame($parent, ParentConnector::getParent($node));

        ParentConnector::setParent($node, null);

        $this->assertNull(ParentConnector::findParent($node));
    }
}
