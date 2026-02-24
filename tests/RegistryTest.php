<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests;

use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Registry\ArrayRegistryDriver;
use DanDoeTech\ResourceRegistry\Registry\Registry;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    public function testLoadsResourcesFromArrayConfig(): void
    {
        /** @var array<string, array<string, mixed>> $config */
        $config = require __DIR__ . '/../examples/config/resources.php';
        $registry = new Registry(new ArrayRegistryDriver($config));

        $product = $registry->getResource('product');
        self::assertNotNull($product);
        self::assertSame('Product', $product->getLabel());
        self::assertCount(4, $product->getFields());
        self::assertSame('name', $product->getFields()[1]->getName());
        self::assertSame(FieldType::String, $product->getFields()[1]->getType());

        $category = $registry->getResource('category');
        self::assertNotNull($category);
        self::assertCount(2, $category->getFields());

        self::assertCount(2, $registry->all());
    }
}
