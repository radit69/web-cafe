<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'user_id',
        'reservation_id',
        'kode',
        'total',
        'daftar_item',
        'metode_pembayaran',
        'status_pembayaran',
        'nama_pelanggan',
        'jumlah_bayar',
        'kembalian',
    ];

    protected $casts = [
        'daftar_item' => 'array',
        'total' => 'integer',
    ];

    public static function generateCode(): string
    {
        do {
            $code = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        } while (static::where('kode', $code)->exists());

        return $code;
    }

    public function getCodeAttribute()
    {
        return $this->kode;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['kode'] = $value;
    }

    public function getItemsAttribute()
    {
        return $this->daftar_item;
    }

    public function setItemsAttribute($value)
    {
        $this->attributes['daftar_item'] = $value;
    }

    public function getPaymentMethodAttribute()
    {
        return $this->metode_pembayaran;
    }

    public function setPaymentMethodAttribute($value)
    {
        $this->attributes['metode_pembayaran'] = $value;
    }

    public function getPaymentStatusAttribute()
    {
        return $this->status_pembayaran;
    }

    public function setPaymentStatusAttribute($value)
    {
        $this->attributes['status_pembayaran'] = $value;
    }

    public function getCustomerNameAttribute()
    {
        return $this->nama_pelanggan;
    }

    public function setCustomerNameAttribute($value)
    {
        $this->attributes['nama_pelanggan'] = $value;
    }
}

