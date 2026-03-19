<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ComputedFieldDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\ComputedFieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ComputedFieldDefinitionTest extends TestCase
{
    #[Test]
    public function implementsInterface(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'category_name',
            type: FieldType::String,
            via: 'category.name',
        );

        self::assertInstanceOf(ComputedFieldDefinitionInterface::class, $computed);
    }

    #[Test]
    public function viaBasedComputedField(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'category_name',
            type: FieldType::String,
            via: 'category.name',
            label: 'Category',
            description: 'Name of the category',
        );

        self::assertSame('category_name', $computed->getName());
        self::assertSame(FieldType::String, $computed->getType());
        self::assertSame('category.name', $computed->getVia());
        self::assertNull($computed->getResolver());
        self::assertSame('Category', $computed->getLabel());
        self::assertSame('Name of the category', $computed->getDescription());
    }

    #[Test]
    public function resolverBasedComputedField(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'revenue',
            type: FieldType::Float,
            resolver: 'App\\Resolvers\\ProductRevenue',
        );

        self::assertSame('revenue', $computed->getName());
        self::assertSame(FieldType::Float, $computed->getType());
        self::assertNull($computed->getVia());
        self::assertSame('App\\Resolvers\\ProductRevenue', $computed->getResolver());
    }

    #[Test]
    public function defaultValuesAreCorrect(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'test',
            type: FieldType::Integer,
        );

        self::assertNull($computed->getVia());
        self::assertNull($computed->getResolver());
        self::assertNull($computed->getLabel());
        self::assertNull($computed->getDescription());
        self::assertSame([], $computed->getMeta());
    }

    #[Test]
    public function countViaPattern(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'orders_count',
            type: FieldType::Integer,
            via: 'count:orders',
        );

        self::assertSame('count:orders', $computed->getVia());
    }

    #[Test]
    public function pluckViaPattern(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'tag_labels',
            type: FieldType::String,
            via: 'pluck:tags.name',
        );

        self::assertSame('pluck:tags.name', $computed->getVia());
    }

    #[Test]
    public function metaIsPreserved(): void
    {
        $computed = new ComputedFieldDefinition(
            name: 'revenue',
            type: FieldType::Float,
            meta: ['currency' => 'EUR', 'precision' => 2],
        );

        self::assertSame(['currency' => 'EUR', 'precision' => 2], $computed->getMeta());
    }
}
