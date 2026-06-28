<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'registration_start',
        'registration_end',
        'start_date',
        'end_date',
        'max_participants',
        'entry_fee',
        'auto_schedule',
    ];

    protected $casts = [
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'auto_schedule' => 'boolean',
        'entry_fee' => 'decimal:2',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TournamentActivityLog::class);
    }

    protected static function booted()
    {
        static::saved(function ($tournament) {
            \Illuminate\Support\Facades\Cache::forget('tournaments_list');
            \Illuminate\Support\Facades\Cache::forget('tournament_show_' . $tournament->id);
        });

        static::deleted(function ($tournament) {
            \Illuminate\Support\Facades\Cache::forget('tournaments_list');
            \Illuminate\Support\Facades\Cache::forget('tournament_show_' . $tournament->id);
        });
    }
}
