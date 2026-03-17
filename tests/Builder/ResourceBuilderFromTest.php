<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Builder;

use DanDoeTech\ResourceRegistry\Builder\ResourceBuilder;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Resource;
use PHPUnit\Framework\TestCase;

final class BaseTestResource extends Resource
{
    protected function define(ResourceBuilder $b): void
    {
        $b->key('base-test')
            ->version(1)
            ->label('Base')
            ->timestamps()
            ->field('name', FieldType::String)
            ->field('price', FieldType::Float)
            ->belongsTo('category', target: 'category', foreignKey: 'category_id')
            ->computed('total', FieldType::Float, via: 'sum:items.price')
            ->filterable(['name', 'price'])
            ->sortable(['name'])
            ->action('create');
    }
}

final class BaseWithSoftDeletesResource extends Resource
{
    protected function define(ResourceBuilder $b): void
    {
        $b->key('soft-base')
            ->version(1)
            ->label('Soft Base')
            ->softDeletes()
            ->field('title', FieldType::String);
    }
}

final class ResourceBuilderFromTest extends TestCase
{
    public function testFromCopiesFieldsFromBaseResource(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->label('Child')
            ->from(BaseTestResource::class)
            ->build();

        self::assertCount(2, $resource->getFields());
        self::assertNotNull($resource->getField('name'));
        self::assertSame(FieldType::String, $resource->getField('name')->getType());
        self::assertNotNull($resource->getField('price'));
        self::assertSame(FieldType::Float, $resource->getField('price')->getType());
    }

    public function testFromCopiesRelationsAndComputed(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->label('Child')
            ->from(BaseTestResource::class)
            ->build();

        self::assertCount(1, $resource->getRelations());
        $rel = $resource->getRelations()[0];
        self::assertSame('category', $rel->getName());
        self::assertSame(RelationType::BelongsTo, $rel->getType());
        self::assertSame('category_id', $rel->getForeignKey());

        self::assertCount(1, $resource->getComputedFields());
        self::assertSame('total', $resource->getComputedFields()[0]->getName());
        self::assertSame('sum:items.price', $resource->getComputedFields()[0]->getVia());
    }

    public function testFromDoesNotCopyKeyOrLabel(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->label('My Child')
            ->from(BaseTestResource::class)
            ->build();

        self::assertSame('child', $resource->getKey());
        self::assertSame('My Child', $resource->getLabel());
    }

    public function testFromDoesNotCopyFilterableOrActions(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->from(BaseTestResource::class)
            ->build();

        self::assertSame([], $resource->getFilterable());
        self::assertSame([], $resource->getSortable());
        self::assertSame([], $resource->getActions());
    }

    public function testFromAllowsOverrideAfterCopy(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->from(BaseTestResource::class)
            ->filterable(['name'])
            ->sortable(['price'])
            ->action('delete')
            ->build();

        self::assertSame(['name'], $resource->getFilterable());
        self::assertSame(['price'], $resource->getSortable());
        self::assertCount(1, $resource->getActions());
        self::assertSame('delete', $resource->getActions()[0]->getName());
    }

    public function testFromCopiesTimestamps(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->from(BaseTestResource::class)
            ->build();

        self::assertTrue($resource->isTimestamped());
    }

    public function testFromCopiesSoftDeletes(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->from(BaseWithSoftDeletesResource::class)
            ->build();

        self::assertTrue($resource->usesSoftDeletes());
        self::assertFalse($resource->isTimestamped());
    }

    public function testFromSkipsDuplicateFields(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->field('name', FieldType::Integer) // Override name as Integer before from()
            ->from(BaseTestResource::class)
            ->build();

        self::assertCount(2, $resource->getFields());
        // The child's own 'name' field (Integer) should be kept, not overwritten by base's String
        $nameField = $resource->getField('name');
        self::assertNotNull($nameField);
        self::assertSame(FieldType::Integer, $nameField->getType());
        self::assertNotNull($resource->getField('price'));
    }

    public function testFromSkipsDuplicateRelations(): void
    {
        $resource = (new ResourceBuilder())
            ->key('child')
            ->hasMany('category', target: 'other-target')
            ->from(BaseTestResource::class)
            ->build();

        self::assertCount(1, $resource->getRelations());
        // Child's own hasMany 'category' should be kept
        self::assertSame(RelationType::HasMany, $resource->getRelations()[0]->getType());
        self::assertSame('other-target', $resource->getRelations()[0]->getTarget());
    }

    public function testRouteSegmentDefaultsToNull(): void
    {
        $resource = (new ResourceBuilder())
            ->key('product')
            ->build();

        self::assertNull($resource->getRouteSegment());
    }

    public function testRouteSegmentCanBeSet(): void
    {
        $resource = (new ResourceBuilder())
            ->key('order')
            ->routeSegment('my/order')
            ->build();

        self::assertSame('my/order', $resource->getRouteSegment());
    }

    public function testRouteSegmentFluentChaining(): void
    {
        $builder = new ResourceBuilder();

        self::assertSame($builder, $builder->routeSegment('test'));
    }

    public function testFromFluentChaining(): void
    {
        $builder = new ResourceBuilder();
        $builder->key('child');

        self::assertSame($builder, $builder->from(BaseTestResource::class));
    }
}
