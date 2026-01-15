<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SleepRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sleep_date',
        'sleep_start',
        'sleep_end',
        'duration_minutes',
        'quality_percent',
        'is_insomnia',
        'time_to_sleep_minutes',
    ];

    protected $casts = [
        'sleep_date' => 'date',
        'is_insomnia' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper to format duration
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        return "{$hours} jam {$minutes} menit";
    }
}
