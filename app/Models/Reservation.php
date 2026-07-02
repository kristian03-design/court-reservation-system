<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_number',
        'user_id',
        'court_id',
        'reservation_date',
        'start_time',
        'end_time',
        'players',
        'total_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('status', ['pending_payment', 'confirmed', 'completed'])
              ->orWhere(function ($sub) {
                  $sub->where('status', 'held')
                      ->where('created_at', '>=', now()->subMinutes(10));
              });
        });
    }

    protected static function booted()
    {
        $invalidateCache = function () {
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');
            \Illuminate\Support\Facades\Cache::forget('admin_reports_data');
        };

        static::saved($invalidateCache);
        static::deleted($invalidateCache);
    }
}
