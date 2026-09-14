<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $table = 'meja';

    protected $primaryKey = 'id_meja';

    protected $fillable = [
        'nomor_meja',
        'kapasitas',
        'status_meja',
    ];

    public function getTableNumberAttribute()
    {
        return $this->nomor_meja;
    }

    public function setTableNumberAttribute($value)
    {
        $this->attributes['nomor_meja'] = $value;
    }

    public function getCapacityAttribute()
    {
        return $this->kapasitas;
    }

    public function setCapacityAttribute($value)
    {
        $this->attributes['kapasitas'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->status_meja;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status_meja'] = $value;
    }
}
