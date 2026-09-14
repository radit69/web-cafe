<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationDetail extends Model
{
    use HasFactory;

    protected $table = 'detail_reservasi';

    protected $primaryKey = 'id_detail_reservasi';

    protected $fillable = [
        'reservation_id',
        'menu_id',
        'jumlah',
        'harga',
        'subtotal',
    ];

    public function getQtyAttribute()
    {
        return $this->jumlah;
    }

    public function setQtyAttribute($value)
    {
        $this->attributes['jumlah'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->harga;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['harga'] = $value;
    }
}
