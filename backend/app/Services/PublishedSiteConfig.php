<?php

namespace App\Services;

use App\Models\SitePublication;

class PublishedSiteConfig
{
    public function __construct(private readonly SiteConfigBuilder $builder) {}

    public function payload(bool $draft = false): array
    {
        if ($draft) return $this->builder->build();
        return SitePublication::query()->latest('version')->value('payload') ?: $this->builder->build();
    }

    public function moduleEnabled(string $slug, bool $draft = false): bool
    {
        $modules = collect($this->payload($draft)['modules'] ?? []);
        $module = $modules->firstWhere('slug', $slug);
        return $module ? (bool) ($module['is_enabled'] ?? false) : false;
    }
}
