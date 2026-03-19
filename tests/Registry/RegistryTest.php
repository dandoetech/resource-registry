<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Registry;

use DanDoeTech\ResourceRegistry\Registry\ArrayRegistryDriver;
use DanDoeTech\ResourceRegistry\Registry\Registry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    #[Test]
    public function getResourceReturnsNullForUnknownKey(): void
    {
        $registry = new Registry(new ArrayRegistryDriver([]));

        self::assertNull($registry->getResource('nonexistent'));
    }

    #[Test]
    public function allReturnsEmptyListWhenNoResources(): void
    {
        $registry = new Registry(new ArrayRegistryDriver([]));

        self::assertSame([], $registry->all());
    }

    #[Test]
    public function allReturnsAllRegisteredResources(): void
    {
        $registry = new Registry(new ArrayRegistryDriver([
            'product' => [
                'label'  => 'Product',
                'fields' => [
                    ['name' => 'name', 'type' => 'string'],
                ],
            ],
            'category' => [
                'label'  => 'Category',
                'fields' => [
                    ['name' => 'name', 'type' => 'string'],
                ],
            ],
        ]));

        $all = $registry->all();
        self::assertCount(2, $all);
        self::assertSame('product', $all[0]->getKey());
        self::assertSame('category', $all[1]->getKey());
    }

    #[Test]
    public function getResourceFindsExistingResource(): void
    {
        $registry = new Registry(new ArrayRegistryDriver([
            'product' => [
                'label'  => 'Product',
                'fields' => [
                    ['name' => 'name', 'type' => 'string'],
                ],
            ],
        ]));

        $product = $registry->getResource('product');
        self::assertNotNull($product);
        self::assertSame('Product', $product->getLabel());
    }
}
