<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'set_number',
        'participant1_score',
        'participant2_score',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(TournamentMatch::class, 'match_id');
    }
}
