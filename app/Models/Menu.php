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
        if ($this->image) {
            $basename = basename($this->image);

            if (file_exists(public_path('menu_images/' . $basename))) {
                return asset('menu_images/' . $basename);
            }

            if (Storage::disk('public')->exists($this->image)) {
                if (file_exists(public_path('storage/' . $this->image))) {
                    return asset('storage/' . $this->image);
                }

                return route('menu.uploaded_image', ['filename' => $basename]);
            }
        }

        $text = urlencode($this->name);
        return "https://placehold.co/400x400/354024/ffffff?text={$text}";
    }
}
