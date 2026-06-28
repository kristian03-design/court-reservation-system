<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    protected $fillable = [
        'court_name',
        'court_type',
        'description',
        'capacity',
        'hourly_rate',
        'image',
        'gallery',
        'amenities',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'gallery' => 'array',
            'amenities' => 'array',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourtSchedule::class);
    }

    public function isOccupiedNow(): bool
    {
        if ($this->status !== 'available') {
            return true;
        }

        $now = now();
        $time = $now->toTimeString();
        $date = $now->toDateString();

        $blockedSchedule = $this->schedules()
            ->whereDate('schedule_date', $date)
            ->whereIn('availability_status', ['reserved', 'maintenance', 'closed'])
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();

        if ($blockedSchedule) {
            return true;
        }

        return $this->reservations()
            ->whereDate('reservation_date', $date)
            ->active()
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }
}
