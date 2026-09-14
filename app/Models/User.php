<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'email_verified_at',
        'google_id',
        'avatar',
        'password',
        'role',
        'is_aktif',
        'login_terakhir',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_aktif' => 'boolean',
        'login_terakhir' => 'datetime',
    ];

    public function getNameAttribute()
    {
        return $this->nama;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    public function getPhoneAttribute()
    {
        return $this->no_hp;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['no_hp'] = $value;
    }

    public function getIsActiveAttribute()
    {
        return $this->is_aktif;
    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_aktif'] = $value;
    }

    public function getLastLoginAtAttribute()
    {
        return $this->login_terakhir;
    }

    public function setLastLoginAtAttribute($value)
    {
        $this->attributes['login_terakhir'] = $value;
    }
}
