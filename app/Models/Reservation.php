<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'guests',
        'reservation_date',
        'reservation_time',
        'notes',
        'table_number',
        'location',
        'order_items',
        'total_amount',
        'dp_amount',
        'dp_status',
        'remaining_amount',
        'cancellation_charge',
        'status',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'order_items' => 'array',
        'total_amount' => 'integer',
        'dp_amount' => 'integer',
        'remaining_amount' => 'integer',
        'cancellation_charge' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->customer_name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => strtoupper(substr($word, 0, 1)))
            ->implode('') ?: 'RS';
    }

    public function getDateLabelAttribute(): string
    {
        return $this->reservation_date->translatedFormat('d F Y');
    }

    public function getTimeLabelAttribute(): string
    {
        return substr((string) $this->reservation_time, 0, 5) . ' WIB';
    }

    public function getStatusLabelAttribute(): string
    {
        $label = [
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? 'Menunggu';

        if ($this->status === 'cancelled' && $this->cancellation_charge) {
            $label .= ' (Charge Rp ' . number_format($this->cancellation_charge, 0, ',', '.') . ')';
        }

        return $label;
    }

    public function getTableLabelAttribute(): ?string
    {
        return $this->table_number ? 'Meja ' . $this->table_number : null;
    }

    public function getLocationLabelAttribute(): string
    {
        $locations = [
            'depok' => 'Depok',
            'cibubur' => 'Cibubur',
        ];

        return $locations[$this->location] ?? 'Belum dipilih';
    }

    public function getDpStatusLabelAttribute(): string
    {
        return [
            'unpaid' => 'DP Belum Dibayar',
            'paid' => 'DP Sudah Dibayar',
            'lunas' => 'Lunas',
        ][$this->dp_status] ?? 'DP Belum Dibayar';
    }

    public function getDpAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->dp_amount, 0, ',', '.');
    }

    public function getRemainingAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 0, ',', '.');
    }

    public function getTotalAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}
