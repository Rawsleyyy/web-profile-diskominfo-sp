<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = ['primary_color_hex', 'accent_color_hex', 'updated_by'];
}
