<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservasi';

    protected $primaryKey = 'id_reservasi';

    protected $fillable = [
        'user_id',
        'kode_reservasi',
        'nama_pelanggan',
        'email_pelanggan',
        'telepon_pelanggan',
        'jumlah_orang',
        'tanggal_reservasi',
        'jam_reservasi',
        'catatan',
        'nomor_meja',
        'lokasi',
        'item_pesanan',
        'total_harga',
        'jumlah_dp',
        'status_dp',
        'sisa_pembayaran',
        'biaya_pembatalan',
        'status_reservasi',
    ];

    protected $casts = [
        'tanggal_reservasi' => 'date',
        'item_pesanan' => 'array',
        'total_harga' => 'integer',
        'jumlah_dp' => 'integer',
        'sisa_pembayaran' => 'integer',
        'biaya_pembatalan' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(function ($reservation) {
            if (is_array($reservation->item_pesanan)) {
                // Delete existing details for sync
                $reservation->details()->delete();

                foreach ($reservation->item_pesanan as $item) {
                    if (isset($item['id'])) {
                        $reservation->details()->create([
                            'menu_id' => $item['id'],
                            'jumlah' => $item['qty'] ?? 1,
                            'harga' => $item['price'] ?? 0,
                            'subtotal' => $item['subtotal'] ?? (($item['qty'] ?? 1) * ($item['price'] ?? 0)),
                        ]);
                    }
                }
            }
        });
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReservationDetail::class);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->nama_pelanggan)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => strtoupper(substr($word, 0, 1)))
            ->implode('') ?: 'RS';
    }

    public function getDateLabelAttribute(): string
    {
        return $this->tanggal_reservasi->translatedFormat('d F Y');
    }

    public function getTimeLabelAttribute(): string
    {
        return substr((string) $this->jam_reservasi, 0, 5) . ' WIB';
    }

    public function getStatusLabelAttribute(): string
    {
        $label = [
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ][$this->status_reservasi] ?? 'Menunggu';

        if ($this->status_reservasi === 'cancelled' && $this->biaya_pembatalan) {
            $label .= ' (Charge Rp ' . number_format($this->biaya_pembatalan, 0, ',', '.') . ')';
        }

        return $label;
    }

    public function getTableLabelAttribute(): ?string
    {
        return $this->nomor_meja ? 'Meja ' . $this->nomor_meja : null;
    }

    public function getLocationLabelAttribute(): string
    {
        $locations = [
            'depok' => 'Depok',
            'cibubur' => 'Cibubur',
        ];

        return $locations[$this->lokasi] ?? 'Belum dipilih';
    }

    public function getDpStatusLabelAttribute(): string
    {
        return [
            'unpaid' => 'DP Belum Dibayar',
            'paid' => 'DP Sudah Dibayar',
            'lunas' => 'Lunas',
        ][$this->status_dp] ?? 'DP Belum Dibayar';
    }

    public function getDpAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_dp, 0, ',', '.');
    }

    public function getRemainingAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->sisa_pembayaran, 0, ',', '.');
    }

    public function getTotalAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function getReservationCodeAttribute()
    {
        return $this->kode_reservasi;
    }

    public function setReservationCodeAttribute($value)
    {
        $this->attributes['kode_reservasi'] = $value;
    }

    public function getCustomerNameAttribute()
    {
        return $this->nama_pelanggan;
    }

    public function setCustomerNameAttribute($value)
    {
        $this->attributes['nama_pelanggan'] = $value;
    }

    public function getCustomerEmailAttribute()
    {
        return $this->email_pelanggan;
    }

    public function setCustomerEmailAttribute($value)
    {
        $this->attributes['email_pelanggan'] = $value;
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->telepon_pelanggan;
    }

    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['telepon_pelanggan'] = $value;
    }

    public function getGuestsAttribute()
    {
        return $this->jumlah_orang;
    }

    public function setGuestsAttribute($value)
    {
        $this->attributes['jumlah_orang'] = $value;
    }

    public function getReservationDateAttribute()
    {
        return $this->tanggal_reservasi;
    }

    public function setReservationDateAttribute($value)
    {
        $this->attributes['tanggal_reservasi'] = $value;
    }

    public function getReservationTimeAttribute()
    {
        return $this->jam_reservasi;
    }

    public function setReservationTimeAttribute($value)
    {
        $this->attributes['jam_reservasi'] = $value;
    }

    public function getNotesAttribute()
    {
        return $this->catatan;
    }

    public function setNotesAttribute($value)
    {
        $this->attributes['catatan'] = $value;
    }

    public function getTableNumberAttribute()
    {
        return $this->nomor_meja;
    }

    public function setTableNumberAttribute($value)
    {
        $this->attributes['nomor_meja'] = $value;
    }

    public function getLocationAttribute()
    {
        return $this->lokasi;
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['lokasi'] = $value;
    }

    public function getOrderItemsAttribute()
    {
        return $this->item_pesanan;
    }

    public function setOrderItemsAttribute($value)
    {
        $this->attributes['item_pesanan'] = $value;
    }

    public function getTotalAmountAttribute()
    {
        return $this->total_harga;
    }

    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_harga'] = $value;
    }

    public function getDpAmountAttribute()
    {
        return $this->jumlah_dp;
    }

    public function setDpAmountAttribute($value)
    {
        $this->attributes['jumlah_dp'] = $value;
    }

    public function getDpStatusAttribute()
    {
        return $this->status_dp;
    }

    public function setDpStatusAttribute($value)
    {
        $this->attributes['status_dp'] = $value;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->sisa_pembayaran;
    }

    public function setRemainingAmountAttribute($value)
    {
        $this->attributes['sisa_pembayaran'] = $value;
    }

    public function getCancellationChargeAttribute()
    {
        return $this->biaya_pembatalan;
    }

    public function setCancellationChargeAttribute($value)
    {
        $this->attributes['biaya_pembatalan'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->status_reservasi;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status_reservasi'] = $value;
    }
}
