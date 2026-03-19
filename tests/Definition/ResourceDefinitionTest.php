<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ResourceDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\ActionDefinition;
use DanDoeTech\ResourceRegistry\Definition\ComputedFieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\QueryProfile;
use DanDoeTech\ResourceRegistry\Definition\RelationDefinition;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceDefinitionTest extends TestCase
{
    #[Test]
    public function implementsInterface(): void
    {
        $resource = new ResourceDefinition(key: 'product', label: 'Product');

        self::assertInstanceOf(ResourceDefinitionInterface::class, $resource);
    }

    #[Test]
    public function getFieldReturnsByName(): void
    {
        $resource = new ResourceDefinition(
            key: 'product',
            label: 'Product',
            fields: [
                new FieldDefinition(name: 'name', type: FieldType::String),
                new FieldDefinition(name: 'price', type: FieldType::Float),
            ],
        );

        $nameField = $resource->getField('name');
        self::assertNotNull($nameField);
        self::assertSame('name', $nameField->getName());

        $priceField = $resource->getField('price');
        self::assertNotNull($priceField);
        self::assertSame(FieldType::Float, $priceField->getType());
    }

    #[Test]
    public function getFieldReturnsNullForUnknown(): void
    {
        $resource = new ResourceDefinition(
            key: 'product',
            label: 'Product',
            fields: [
                new FieldDefinition(name: 'name', type: FieldType::String),
            ],
        );

        self::assertNull($resource->getField('nonexistent'));
    }

    #[Test]
    public function getQueryProfilesReturnsProfiles(): void
    {
        $profile = new QueryProfile(
            filterable: ['name', 'price'],
            sortable: ['name'],
        );

        $resource = new ResourceDefinition(
            key: 'product',
            label: 'Product',
            queryProfiles: ['admin' => $profile],
        );

        $profiles = $resource->getQueryProfiles();
        self::assertCount(1, $profiles);
        self::assertArrayHasKey('admin', $profiles);
        self::assertSame(['name', 'price'], $profiles['admin']->filterable);
    }

    #[Test]
    public function getQueryProfilesDefaultsToEmpty(): void
    {
        $resource = new ResourceDefinition(key: 'product', label: 'Product');

        self::assertSame([], $resource->getQueryProfiles());
    }

    #[Test]
    public function getRouteSegmentReturnsValue(): void
    {
        $resource = new ResourceDefinition(
            key: 'order-item',
            label: 'Order Item',
            routeSegment: 'order-items',
        );

        self::assertSame('order-items', $resource->getRouteSegment());
    }

    #[Test]
    public function getRouteSegmentDefaultsToNull(): void
    {
        $resource = new ResourceDefinition(key: 'product', label: 'Product');

        self::assertNull($resource->getRouteSegment());
    }

    #[Test]
    public function defaultValues(): void
    {
        $resource = new ResourceDefinition(key: 'item', label: 'Item');

        self::assertSame('item', $resource->getKey());
        self::assertSame('Item', $resource->getLabel());
        self::assertSame(1, $resource->getVersion());
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

    #[Test]
    public function fullResourceWithAllProperties(): void
    {
        $resource = new ResourceDefinition(
            key: 'product',
            label: 'Product',
            fields: [
                new FieldDefinition(name: 'name', type: FieldType::String),
            ],
            relations: [
                new RelationDefinition(name: 'category', type: RelationType::BelongsTo, target: 'category'),
            ],
            actions: [
                new ActionDefinition(name: 'create'),
            ],
            description: 'A product',
            version: 2,
            timestamps: true,
            softDeletes: true,
            computedFields: [
                new ComputedFieldDefinition(name: 'category_name', type: FieldType::String, via: 'category.name'),
            ],
            filterable: ['name'],
            sortable: ['name'],
            searchable: ['name'],
            meta: ['icon' => 'box'],
            routeSegment: 'products',
            queryProfiles: ['admin' => new QueryProfile(filterable: ['name'])],
        );

        self::assertSame(2, $resource->getVersion());
        self::assertSame('A product', $resource->getDescription());
        self::assertTrue($resource->isTimestamped());
        self::assertTrue($resource->usesSoftDeletes());
        self::assertCount(1, $resource->getFields());
        self::assertCount(1, $resource->getRelations());
        self::assertCount(1, $resource->getActions());
        self::assertCount(1, $resource->getComputedFields());
        self::assertSame(['name'], $resource->getFilterable());
        self::assertSame(['name'], $resource->getSortable());
        self::assertSame(['name'], $resource->getSearchable());
        self::assertSame(['icon' => 'box'], $resource->getMeta());
        self::assertSame('products', $resource->getRouteSegment());
        self::assertCount(1, $resource->getQueryProfiles());
    }
}
