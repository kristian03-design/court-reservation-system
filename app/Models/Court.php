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

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
