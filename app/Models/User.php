<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'hashed_password',
        'reset_token',
    ];

    protected $hidden = [
        'hashed_password',
        'reset_token',
    ];

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function getAuthPassword()
    {
        return $this->hashed_password;
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function sleepRecords()
    {
        return $this->hasMany(SleepRecord::class);
    }

    public function insomniaAlerts()
    {
        return $this->hasMany(InsomniaAlert::class);
    }
}
