<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = [
        'primary_color_hex', 'secondary_color_hex', 'accent_color_hex', 'background_color_hex',
        'surface_color_hex', 'text_primary_hex', 'text_secondary_hex', 'preset_key',
        'font_heading', 'font_body', 'radius_style', 'button_style', 'card_style',
        'container_width', 'navbar_style', 'color_mode', 'draft_config', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['draft_config' => 'array'];
    }

    public function publishedConfig(): array
    { 
        return collect($this->only([
            'primary_color_hex', 'secondary_color_hex', 'accent_color_hex', 'background_color_hex',
            'surface_color_hex', 'text_primary_hex', 'text_secondary_hex', 'preset_key',
            'font_heading', 'font_body', 'radius_style', 'button_style', 'card_style',
            'container_width', 'navbar_style', 'color_mode',
        ]))->all();
    }
}
