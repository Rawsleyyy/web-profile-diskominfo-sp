<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitePublication extends Model
{
    protected $fillable = ['version', 'label', 'payload', 'published_by'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'version' => 'integer'];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
