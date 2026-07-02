<?php

namespace App\Models;

use Carbon\Carbon;
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
        'price'            => 'decimal:2',
        'max_slots'        => 'integer',
        'registered'       => 'integer',
        'requires_payment' => 'boolean',
        'allow_waitlist'   => 'boolean',
        'featured'         => 'boolean',
        'published'        => 'boolean',
    ];

    /**
     * Always return a Carbon instance for start_date, regardless of DB driver.
     * On Vercel's PostgreSQL driver, dates can come back as plain strings.
     */
    public function getStartDateAttribute($value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getEndDateAttribute($value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }
}

