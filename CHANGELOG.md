# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-03-15

### Added
- `ResourceDefinitionInterface`, `FieldDefinitionInterface`, `RelationDefinitionInterface`, `ActionDefinitionInterface`, `ComputedFieldDefinitionInterface` contracts
- `RegistryDriverInterface` contract for pluggable registry backends
- Final class Value Objects implementing all interfaces (`ResourceDefinition`, `FieldDefinition`, `RelationDefinition`, `ActionDefinition`, `ComputedFieldDefinition`)
- `ResourceBuilder` with fluent API for defining resources (fields, relations, computed fields, actions, filterable/sortable/searchable lists)
- Builder validation: duplicate field/relation detection, required key check, auto-generated label
- Abstract `Resource` base class with lazy-build pattern via `define(ResourceBuilder)`
- `Registry` class with `all()` and `getResource()` methods
- `ArrayRegistryDriver` for testing and standalone use (hydrates VOs from array config)
- `FieldType` enum: String, Integer, Float, Boolean, DateTime, Json
- `RelationType` enum: BelongsTo, HasOne, HasMany, BelongsToMany, HasManyThrough, HasOneThrough, MorphTo, MorphMany
- `getMeta()` escape hatch on all interfaces for custom extensions
- Field properties: nullable, rules, unique, indexed, default, comment
- Resource properties: version, timestamps, softDeletes, description
- Relation properties: foreignKey, relatedKey, pivotTable
- Computed field properties: `via` (generic resolution) and `resolver` (custom class)
