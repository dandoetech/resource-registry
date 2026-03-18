<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

/**
 * A named query profile that overrides filterable/sortable/searchable
 * fields and optionally applies automatic pre-filter conditions.
 */
final class QueryProfile
{
    /**
     * @param list<string>|null    $filterable null = use resource default
     * @param list<string>|null    $sortable   null = use resource default
     * @param array<string, mixed> $preFilter  auto-applied WHERE conditions (exact match)
     * @param list<string>|null    $searchable null = use resource default
     */
    public function __construct(
        public readonly ?array $filterable = null,
        public readonly ?array $sortable = null,
        public readonly array $preFilter = [],
        public readonly ?array $searchable = null,
    ) {
    }
}
