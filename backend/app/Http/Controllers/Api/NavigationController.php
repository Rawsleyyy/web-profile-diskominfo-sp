<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\SiteModule;
use Illuminate\Http\JsonResponse;

class NavigationController extends Controller
{
    public function index(): JsonResponse
    {
        $enabledModules = SiteModule::query()->where('is_enabled', true)->pluck('public_route', 'slug');

        $items = MenuItem::query()
            ->with(['page:id,title,slug,is_published,published_at', 'children.page:id,title,slug,is_published,published_at'])
            ->whereNull('parent_id')
            ->visibleNow()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (MenuItem $item) => $this->transform($item, $enabledModules->all()))
            ->filter()
            ->values();

        return response()->json(['data' => $items]);
    }

    private function transform(MenuItem $item, array $enabledModules): ?array
    {
        if ($item->type === 'module' && ! array_key_exists((string) $item->module_slug, $enabledModules)) {
            return null;
        }

        if ($item->type === 'page' && (! $item->page || ! $item->page->is_published || ($item->page->published_at && $item->page->published_at->isFuture()))) {
            return null;
        }

        $children = $item->children
            ->filter(function (MenuItem $child) use ($enabledModules) {
                if (! $child->is_active) {
                    return false;
                }
                if ($child->visible_from && $child->visible_from->isFuture()) {
                    return false;
                }
                if ($child->visible_until && $child->visible_until->isPast()) {
                    return false;
                }
                if ($child->type === 'module' && ! array_key_exists((string) $child->module_slug, $enabledModules)) {
                    return false;
                }
                if ($child->type === 'page' && (! $child->page || ! $child->page->is_published || ($child->page->published_at && $child->page->published_at->isFuture()))) {
                    return false;
                }
                return true;
            })
            ->map(fn (MenuItem $child) => $this->transform($child, $enabledModules))
            ->filter()
            ->values();

        return [
            'id' => $item->id,
            'label' => $item->label,
            'type' => $item->type,
            'url' => $this->resolveUrl($item, $enabledModules),
            'target' => $item->target,
            'is_external' => $item->type === 'external' || $item->target === '_blank',
            'children' => $children,
        ];
    }

    private function resolveUrl(MenuItem $item, array $enabledModules): string
    {
        return match ($item->type) {
            'module' => $enabledModules[$item->module_slug] ?? '#',
            'page' => $item->page ? '/page/'.$item->page->slug : '#',
            'dropdown' => '#',
            default => $item->url ?: '#',
        };
    }
}
