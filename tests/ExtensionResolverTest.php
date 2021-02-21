<?php

declare(strict_types=1);

namespace PHPMND\Tests;

use function get_class;
use InvalidArgumentException;
use PHPMND\Extension\AssignExtension;
use PHPMND\Extension\ReturnExtension;
use PHPMND\ExtensionResolver;
use PHPUnit\Framework\TestCase;

class ExtensionResolverTest extends TestCase
{
    public function test_resolve_default(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve([]);

        $this->assertSame($resolver->defaults(), $extensions);
    }

    public function test_resolve_add_extension(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['assign']);

        foreach ($extensions as $extension) {
            if (get_class($extension) === AssignExtension::class) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->assertTrue(false);
    }

    public function test_resolve_all(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['all']);

        $this->assertSame($resolver->all(), $extensions);
    }

    public function test_resolve_with_minus(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['-return']);

        foreach ($extensions as $extension) {
            if (get_class($extension) === ReturnExtension::class) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    public function test_resolve_not_existing_extension(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $resolver = $this->createResolver();
        $resolver->resolve(['not_existing']);
    }

    private function createResolver(): ExtensionResolver
    {
        return new ExtensionResolver();
    }
}
