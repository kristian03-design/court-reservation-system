<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'sport',
        'event_type',
        'description',
        'image',
        'price',
        'max_slots',
        'registered',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'court_id',
        'requires_payment',
        'allow_waitlist',
        'featured',
        'published',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'max_slots' => 'integer',
        'registered' => 'integer',
        'requires_payment' => 'boolean',
        'allow_waitlist' => 'boolean',
        'featured' => 'boolean',
        'published' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }
}
