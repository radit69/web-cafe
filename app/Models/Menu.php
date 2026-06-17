<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'image',
        'price',
        'stock',
        'status',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        // Check storage/public first (for local dev / uploaded images)
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // Fallback: check public/menu_images (for production/Railway)
        if ($this->image) {
            $basename = basename($this->image);
            if (file_exists(public_path('menu_images/' . $basename))) {
                return asset('menu_images/' . $basename);
            }
        }

        $text = urlencode($this->name);
        return "https://placehold.co/400x400/354024/ffffff?text={$text}";
    }
}
