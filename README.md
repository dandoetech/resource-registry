# DanDoeTech ResourceRegistry

> **Framework-agnostic metadata layer** for describing API resources, fields, relations, actions, and policies —
> the single **source of truth** for your domain contracts across REST, OpenAPI, and BFF layers.

![Tests](https://github.com/dandoetech/resource-registry/actions/workflows/tests.yml/badge.svg)
![Static Analysis](https://github.com/dandoetech/resource-registry/actions/workflows/static-analysis.yml/badge.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

## Purpose

Modern backends suffer from duplication: models define schema, controllers define validation, OpenAPI specs repeat them,
and frontends duplicate field definitions again. **`resource-registry`** centralizes all this information into a single,
typed, framework-independent **Resource Definition Model** that is consumable by API generators, OpenAPI generators,
and BFF metadata providers.

**Define your resource once — and every layer knows how to use it.**

## Design Principles

- **Framework-agnostic**: Works standalone, no Laravel dependency.
- **Typed contracts**: PHP 8.2+ enums and value objects for safe metadata.
- **Extensible drivers**: Array/config driver now, DB or code-introspection drivers later.
- **Composable layers**: Consumers (API, OpenAPI, BFF) depend on contracts, not implementation.
- **Declarative > imperative**: Define *what* a resource is, not *how* it behaves at runtime.

## Example: Defining Resources

```php
<?php

declare(strict_types=1);

use DanDoeTech\ResourceRegistry\Definition\{
    FieldDefinition, FieldType, RelationDefinition, RelationType, ActionDefinition
};

return [
    'product' => [
        'label' => 'Product',
        'fields' => [
            new FieldDefinition('id', FieldType::Integer, false),
            new FieldDefinition('name', FieldType::String, false, ['required', 'max:120']),
            new FieldDefinition('price', FieldType::Float, false, ['min:0']),
            new FieldDefinition('created_at', FieldType::DateTime, false),
        ],
        'relations' => [
            new RelationDefinition('category', RelationType::BelongsTo, 'category'),
        ],
        'actions' => [
            new ActionDefinition('create'),
            new ActionDefinition('update'),
        ],
    ],

    'category' => [
        'label' => 'Category',
        'fields' => [
            new FieldDefinition('id', FieldType::Integer, false),
            new FieldDefinition('name', FieldType::String, false, ['required']),
        ],
        'relations' => [
            new RelationDefinition('products', RelationType::HasMany, 'product'),
        ],
        'actions' => [
            new ActionDefinition('create'),
        ],
    ],
];
```

## Usage

```php
use DanDoeTech\ResourceRegistry\Registry\ArrayRegistryDriver;
use DanDoeTech\ResourceRegistry\Registry\Registry;

$config = require __DIR__.'/config/resources.php';

$driver = new ArrayRegistryDriver($config);
$registry = new Registry($driver);

$product = $registry->getResource('product'); // ResourceDefinition
foreach ($registry->all() as $resource) {
    echo $resource->label . PHP_EOL;
}
```

## When to Use

- Generate **consistent REST APIs** automatically
- Produce **OpenAPI/Swagger specs** from typed metadata
- Feed **frontend apps (Vue, React, etc.)** with **BFF metadata**
- Avoid repeating the same field/validation/relation definitions across layers
- Share **resource contracts** between multiple services

## What It Is *Not*

- ❌ Not an ORM or a replacement for Eloquent
- ❌ Not a data runtime or query layer
- ✅ It’s **metadata**, not **data**

## Architecture (DDD-style)

```
App (Laravel/Symfony)
        |
        v
 Resource Registry  ←  this package
        |
        v
+---------------+---------------+---------------+
|  Generic API  |  OpenAPI Doc  |  BFF Metadata |
+---------------+---------------+---------------+
```

## API Overview

| Class                        | Description                                          |
|------------------------------|------------------------------------------------------|
| `Registry`                   | Central access point for resources                   |
| `RegistryDriverInterface`    | Contract for any driver providing metadata           |
| `ArrayRegistryDriver`        | Simple implementation using PHP arrays               |
| `ResourceDefinition`         | Describes a resource with fields, relations, actions |
| `FieldDefinition`            | Describes an individual field                        |
| `RelationDefinition`         | Describes a relation to another resource             |
| `ActionDefinition`           | Describes available actions (create, update, etc.)   |
| `FieldType` / `RelationType` | Enums for consistent type usage                      |

## Extending It

Implement a custom driver:

```php
use DanDoeTech\ResourceRegistry\Contracts\RegistryDriverInterface;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;

final class DatabaseRegistryDriver implements RegistryDriverInterface
{
    /** @return list<ResourceDefinition> */
    public function all(): array { /* ... */ }

    public function find(string $key): ?ResourceDefinition { /* ... */ }
}
```

## Installation

```bash
composer require dandoetech/resource-registry
```

## Testing

```bash
composer install
composer test
```

## Roadmap

- [ ] Policy/permission metadata
- [ ] JSON Schema export
- [ ] Laravel bridge (service provider + config publish)
- [ ] CLI for registry validation and schema export

## Ecosystem

This package is part of the **dandoetech** open-source ecosystem — a modular set of libraries enabling
contract-driven backends: **Resource Registry → OpenAPI → Generic API → BFF metadata**.

## License

MIT © Danilo Doelle <oss@dandoe.tech>
