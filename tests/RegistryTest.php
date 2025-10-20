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
        $config = require __DIR__ . '/../examples/config/resources.php';
        $registry = new Registry(new ArrayRegistryDriver($config));

        $product = $registry->getResource('product');
        self::assertNotNull($product);
        self::assertSame('Product', $product->label);
        self::assertCount(4, $product->fields);
        self::assertSame('name', $product->fields[1]->name);
        self::assertSame(FieldType::String, $product->fields[1]->type);

        $category = $registry->getResource('category');
        self::assertNotNull($category);
        self::assertCount(2, $category->fields);

        self::assertCount(2, $registry->all());
    }
}
