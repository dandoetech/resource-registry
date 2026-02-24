<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests;

use DanDoeTech\ResourceRegistry\Builder\ResourceBuilder;
use DanDoeTech\ResourceRegistry\Contracts\ResourceDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Resource;
use PHPUnit\Framework\TestCase;

final class TestProductResource extends Resource
{
    public int $defineCallCount = 0;

    protected function define(ResourceBuilder $builder): void
    {
        ++$this->defineCallCount;

        $builder
            ->key('product')
            ->version(2)
            ->label('Product')
            ->description('A product in the catalog')
            ->timestamps()
            ->softDeletes()
            ->field('name', FieldType::String, nullable: false, rules: ['required', 'max:120'])
            ->field('price', FieldType::Float, nullable: false, rules: ['min:0'])
            ->field('category_id', FieldType::Integer, nullable: false)
            ->belongsTo('category', foreignKey: 'category_id')
            ->hasMany('reviews')
            ->computed('category_name', FieldType::String, via: 'category.name')
            ->computed('orders_count', FieldType::Integer, via: 'count:orders')
            ->filterable(['name', 'price', 'category_name'])
            ->sortable(['name', 'price', 'orders_count'])
            ->searchable(['name'])
            ->action('create')
            ->action('update')
            ->action('delete')
            ->meta(['icon' => 'box']);
    }
}

final class TestMinimalResource extends Resource
{
    protected function define(ResourceBuilder $builder): void
    {
        $builder->key('minimal');
    }
}

final class ResourceTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $resource = new TestProductResource();

        self::assertInstanceOf(ResourceDefinitionInterface::class, $resource);
    }

    public function testDefineIsCalledExactlyOnce(): void
    {
        $resource = new TestProductResource();

        self::assertSame(0, $resource->defineCallCount);

        // First access triggers define()
        $resource->getKey();
        self::assertSame(1, $resource->defineCallCount);

        // Subsequent accesses do not call define() again
        $resource->getLabel();
        $resource->getFields();
        $resource->getRelations();
        $resource->getActions();
        $resource->getComputedFields();
        $resource->getMeta();
        self::assertSame(1, $resource->defineCallCount);
    }

    public function testKeyAndMetadata(): void
    {
        $resource = new TestProductResource();

        self::assertSame('product', $resource->getKey());
        self::assertSame(2, $resource->getVersion());
        self::assertSame('Product', $resource->getLabel());
        self::assertSame('A product in the catalog', $resource->getDescription());
        self::assertSame(['icon' => 'box'], $resource->getMeta());
    }

    public function testTimestampsAndSoftDeletes(): void
    {
        $resource = new TestProductResource();

        self::assertTrue($resource->isTimestamped());
        self::assertTrue($resource->usesSoftDeletes());
    }

    public function testFields(): void
    {
        $resource = new TestProductResource();

        self::assertCount(3, $resource->getFields());

        $name = $resource->getField('name');
        self::assertNotNull($name);
        self::assertSame('name', $name->getName());
        self::assertSame(FieldType::String, $name->getType());
        self::assertFalse($name->isNullable());
        self::assertSame(['required', 'max:120'], $name->getRules());

        $price = $resource->getField('price');
        self::assertNotNull($price);
        self::assertSame(FieldType::Float, $price->getType());

        self::assertNull($resource->getField('nonexistent'));
    }

    public function testRelations(): void
    {
        $resource = new TestProductResource();

        self::assertCount(2, $resource->getRelations());

        $category = $resource->getRelations()[0];
        self::assertSame('category', $category->getName());
        self::assertSame(RelationType::BelongsTo, $category->getType());
        self::assertSame('category', $category->getTarget());
        self::assertSame('category_id', $category->getForeignKey());

        $reviews = $resource->getRelations()[1];
        self::assertSame('reviews', $reviews->getName());
        self::assertSame(RelationType::HasMany, $reviews->getType());
    }

    public function testComputedFields(): void
    {
        $resource = new TestProductResource();

        self::assertCount(2, $resource->getComputedFields());

        $categoryName = $resource->getComputedFields()[0];
        self::assertSame('category_name', $categoryName->getName());
        self::assertSame(FieldType::String, $categoryName->getType());
        self::assertSame('category.name', $categoryName->getVia());
        self::assertNull($categoryName->getResolver());

        $ordersCount = $resource->getComputedFields()[1];
        self::assertSame('orders_count', $ordersCount->getName());
        self::assertSame('count:orders', $ordersCount->getVia());
    }

    public function testActions(): void
    {
        $resource = new TestProductResource();

        self::assertCount(3, $resource->getActions());
        self::assertSame('create', $resource->getActions()[0]->getName());
        self::assertSame('update', $resource->getActions()[1]->getName());
        self::assertSame('delete', $resource->getActions()[2]->getName());
    }

    public function testFilterableSortableSearchable(): void
    {
        $resource = new TestProductResource();

        self::assertSame(['name', 'price', 'category_name'], $resource->getFilterable());
        self::assertSame(['name', 'price', 'orders_count'], $resource->getSortable());
        self::assertSame(['name'], $resource->getSearchable());
    }

    public function testMinimalResourceDefaultsVersionToOne(): void
    {
        $resource = new TestMinimalResource();

        self::assertSame('minimal', $resource->getKey());
        self::assertSame(1, $resource->getVersion());
        self::assertSame('Minimal', $resource->getLabel());
        self::assertNull($resource->getDescription());
        self::assertFalse($resource->isTimestamped());
        self::assertFalse($resource->usesSoftDeletes());
        self::assertSame([], $resource->getFields());
        self::assertSame([], $resource->getRelations());
        self::assertSame([], $resource->getActions());
        self::assertSame([], $resource->getComputedFields());
        self::assertSame([], $resource->getFilterable());
        self::assertSame([], $resource->getSortable());
        self::assertSame([], $resource->getSearchable());
        self::assertSame([], $resource->getMeta());
    }
}
