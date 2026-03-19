# DanDoeTech Resource Registry

Framework-agnostic resource definition layer for PHP. Define your API resources once — fields, relations, actions, computed fields — and let consumer packages (OpenAPI, BFF, generic API) derive everything else.

## Installation

```bash
composer require dandoetech/resource-registry
```

## Quick Start

Define a resource by extending the `Resource` base class:

```php
use DanDoeTech\ResourceRegistry\Resource;
use DanDoeTech\ResourceRegistry\Builder\ResourceBuilder;
use DanDoeTech\ResourceRegistry\Definition\FieldType;

class ProductResource extends Resource
{
    protected function define(ResourceBuilder $b): void
    {
        $b->key('product')
          ->label('Product')
          ->timestamps()
          ->field('name', FieldType::String, nullable: false, rules: ['required', 'max:120'])
          ->field('price', FieldType::Float, nullable: false, rules: ['min:0'])
          ->field('category_id', FieldType::Integer, nullable: false)
          ->belongsTo('category', foreignKey: 'category_id')
          ->hasMany('reviews')
          ->computed('category_name', FieldType::String, via: 'category.name')
          ->computed('orders_count', FieldType::Integer, via: 'count:orders')
          ->filterable(['name', 'price', 'category_name'])
          ->sortable(['name', 'price', 'created_at', 'orders_count'])
          ->searchable(['name'])
          ->action('create')
          ->action('update')
          ->action('delete');
    }
}
```

Use the registry to access resource definitions:

```php
use DanDoeTech\ResourceRegistry\Registry\ArrayRegistryDriver;
use DanDoeTech\ResourceRegistry\Registry\Registry;

// For standalone/testing use, ArrayRegistryDriver accepts array configs
$driver = new ArrayRegistryDriver([
    'product' => [
        'label' => 'Product',
        'fields' => [
            new FieldDefinition('name', FieldType::String, nullable: false),
            new FieldDefinition('price', FieldType::Float, nullable: false),
        ],
        'actions' => [new ActionDefinition('create')],
    ],
]);
$registry = new Registry($driver);

$product = $registry->getResource('product');
echo $product->getLabel(); // "Product"

foreach ($product->getFields() as $field) {
    echo $field->getName() . ': ' . $field->getType()->value;
}
```

> **Laravel users:** Use [`dandoetech/laravel-resource-registry`](https://github.com/dandoetech/laravel-resource-registry) for automatic class-based discovery, service provider binding, and Eloquent integration.

## API Overview

### Contracts

All consumer packages type against these interfaces, never concrete classes.

| Interface | Purpose |
|---|---|
| `ResourceDefinitionInterface` | Fields, relations, actions, computed fields, filtering/sorting config |
| `FieldDefinitionInterface` | Name, type, nullable, rules, unique, indexed, default, meta |
| `RelationDefinitionInterface` | Name, type, target resource, foreign/related keys, pivot table |
| `ActionDefinitionInterface` | Name, description, meta |
| `ComputedFieldDefinitionInterface` | Name, type, `via` (generic) or `resolver` (custom class) |
| `RegistryDriverInterface` | `all()` and `find(string $key)` — pluggable backends |

### Enums

| Enum | Values |
|---|---|
| `FieldType` | `String`, `Integer`, `Float`, `Boolean`, `DateTime`, `Json`, `Date`, `Text`, `Email`, `Url`, `Enum` |
| `RelationType` | `BelongsTo`, `HasOne`, `HasMany`, `BelongsToMany`, `HasManyThrough`, `HasOneThrough`, `MorphTo`, `MorphMany` |

### Core Classes

| Class | Role |
|---|---|
| `Resource` | Abstract base class — extend and implement `define(ResourceBuilder)` |
| `ResourceBuilder` | Fluent API for defining resources inside `define()` |
| `Registry` | Main access point: `all()` and `getResource(string $key)` |
| `ArrayRegistryDriver` | Array-config driver for standalone use and testing |

### Value Objects

`ResourceDefinition`, `FieldDefinition`, `RelationDefinition`, `ActionDefinition`, `ComputedFieldDefinition` — `final class` implementations of their interfaces. Used internally by drivers and tests.

## Computed Fields

Flat, derived columns for list views. Two strategies:

```php
// Generic — system builds the query via relation metadata
->computed('category_name', FieldType::String, via: 'category.name')
->computed('orders_count', FieldType::Integer, via: 'count:orders')

// Custom — your resolver class handles the query
->computed('revenue', FieldType::Float, resolver: ProductRevenue::class)
```

Computed fields can be listed in `filterable()` and `sortable()` like regular fields.

## Extending

Implement `RegistryDriverInterface` for custom backends:

```php
use DanDoeTech\ResourceRegistry\Contracts\RegistryDriverInterface;

final class DatabaseRegistryDriver implements RegistryDriverInterface
{
    /** @return list<ResourceDefinitionInterface> */
    public function all(): array { /* ... */ }

    public function find(string $key): ?ResourceDefinitionInterface { /* ... */ }
}
```

Every interface has a `getMeta(): array` escape hatch for custom extensions.

## Testing

```bash
composer install
composer test        # PHPUnit
composer qa          # cs:check + phpstan + test
```

## License

MIT
