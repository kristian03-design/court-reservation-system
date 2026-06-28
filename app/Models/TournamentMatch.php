<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TournamentMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'bracket_type',
        'round_number',
        'match_number',
        'participant1_id',
        'participant2_id',
        'winner_id',
        'loser_id',
        'next_match_id',
        'loser_next_match_id',
        'status',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function participant1(): BelongsTo
    {
        return $this->belongsTo(TournamentParticipant::class, 'participant1_id');
    }

    public function participant2(): BelongsTo
    {
        return $this->belongsTo(TournamentParticipant::class, 'participant2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(TournamentParticipant::class, 'winner_id');
    }

    public function loser(): BelongsTo
    {
        return $this->belongsTo(TournamentParticipant::class, 'loser_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(TournamentMatch::class, 'next_match_id');
    }

    public function loserNextMatch(): BelongsTo
    {
        return $this->belongsTo(TournamentMatch::class, 'loser_next_match_id');
    }

    public function sets(): HasMany
    {
        return $this->hasMany(MatchSet::class, 'match_id');
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(TournamentSchedule::class, 'match_id');
    }
}
