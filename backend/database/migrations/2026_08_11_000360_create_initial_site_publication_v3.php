<?php

use App\Models\SitePublication;
use App\Services\SiteConfigBuilder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (! SitePublication::query()->exists()) {
            SitePublication::create([
                'version' => 1,
                'label' => 'Baseline sebelum CMS V3',
                'payload' => app(SiteConfigBuilder::class)->build(),
                'published_by' => null,
            ]);
        }
    }

    public function down(): void
    {
        SitePublication::query()->where('label', 'Baseline sebelum CMS V3')->delete();
    }
};
