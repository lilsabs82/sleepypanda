<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsomniaAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hours_without_sleep',
        'avg_duration_minutes',
        'alert_date',
    ];

    protected $casts = [
        'alert_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->avg_duration_minutes / 60);
        $minutes = $this->avg_duration_minutes % 60;
        if ($hours > 0) {
            return "{$hours} Jam {$minutes} Menit";
        }
        return "{$minutes} Menit";
    }
}
