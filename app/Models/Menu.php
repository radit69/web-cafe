<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'nama_menu',
        'kategori',
        'deskripsi',
        'gambar',
        'harga',
        'stok',
        'status',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if ($this->gambar) {
            $basename = basename($this->gambar);

            if (file_exists(public_path('menu_images/' . $basename))) {
                return asset('menu_images/' . $basename);
            }

            if (Storage::disk('public')->exists($this->gambar)) {
                if (file_exists(public_path('storage/' . $this->gambar))) {
                    return asset('storage/' . $this->gambar);
                }

                return route('menu.uploaded_image', ['filename' => $basename]);
            }
        }

        $text = urlencode($this->nama_menu);
        return "https://placehold.co/400x400/354024/ffffff?text={$text}";
    }

    public function getNameAttribute()
    {
        return $this->nama_menu;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama_menu'] = $value;
    }

    public function getCategoryAttribute()
    {
        return $this->kategori;
    }

    public function setCategoryAttribute($value)
    {
        $this->attributes['kategori'] = $value;
    }

    public function getDescriptionAttribute()
    {
        return $this->deskripsi;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['deskripsi'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->harga;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['harga'] = $value;
    }

    public function getStockAttribute()
    {
        return $this->stok;
    }

    public function setStockAttribute($value)
    {
        $this->attributes['stok'] = $value;
    }
}
