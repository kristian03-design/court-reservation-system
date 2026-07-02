<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Court;
use App\Models\User;
use App\Models\EventRegistration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $court = Court::first();
        $courtId = $court ? $court->id : null;

        // Clear existing events and registrations
        EventRegistration::truncate();
        Event::truncate();

        // Create initial events
        $eventsData = [
            [
                'title' => 'Weekend Social Mixer',
                'slug' => 'weekend-social-mixer',
                'sport' => 'Social Play',
                'event_type' => 'Weekly Session',
                'description' => 'Show up solo or with friends. We organize casual matches and connect you with players of similar skill levels. Perfect for networking, scrimmage sessions, and community play.',
                'image' => 'images/event-social-mixer.webp',
                'price' => 150.00,
                'max_slots' => 48,
                'registered' => 0,
                'start_date' => now()->next('Saturday')->toDateString(),
                'end_date' => now()->next('Saturday')->toDateString(),
                'start_time' => '16:00:00',
                'end_time' => '20:00:00',
                'location' => 'CourtConnect Main Arena',
                'court_id' => $courtId,
                'requires_payment' => true,
                'allow_waitlist' => true,
                'featured' => true,
                'published' => true,
                'status' => 'open',
            ],
            [
                'title' => 'Doubles Serve & Volley Clinic',
                'slug' => 'doubles-serve-volley-clinic',
                'sport' => 'Tennis',
                'event_type' => 'Skills Clinic',
                'description' => 'Master positioning, defensive returns, and advanced volley techniques through guided sessions with experienced coaches.',
                'image' => 'images/event-tennis-clinic.webp',
                'price' => 500.00,
                'max_slots' => 20,
                'registered' => 0,
                'start_date' => '2026-08-25',
                'end_date' => '2026-08-25',
                'start_time' => '08:00:00',
                'end_time' => '11:00:00',
                'location' => 'Tennis Court A',
                'court_id' => $courtId,
                'requires_payment' => true,
                'allow_waitlist' => true,
                'featured' => false,
                'published' => true,
                'status' => 'open',
            ],
            [
                'title' => 'Youth Basketball Camp',
                'slug' => 'youth-basketball-camp',
                'sport' => 'Basketball',
                'event_type' => 'Training Camp',
                'description' => 'Comprehensive developmental basketball program for ages 8–15 focusing on shooting, dribbling, team play, and court awareness.',
                'image' => 'images/event-basketball-camp.webp',
                'price' => 2500.00,
                'max_slots' => 40,
                'registered' => 0,
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-03',
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'location' => 'Main Indoor Basketball Court',
                'court_id' => $courtId,
                'requires_payment' => true,
                'allow_waitlist' => true,
                'featured' => true,
                'published' => true,
                'status' => 'open',
            ],
            [
                'title' => 'Padel Open Night',
                'slug' => 'padel-open-night',
                'sport' => 'Padel',
                'event_type' => 'Open Play',
                'description' => 'Evening social and competitive matches with rotating opponents.',
                'image' => 'images/event-padel-night.webp',
                'price' => 300.00,
                'max_slots' => 32,
                'registered' => 0,
                'start_date' => now()->next('Friday')->toDateString(),
                'end_date' => now()->next('Friday')->toDateString(),
                'start_time' => '18:00:00',
                'end_time' => '22:00:00',
                'location' => 'Padel Court 1',
                'court_id' => $courtId,
                'requires_payment' => true,
                'allow_waitlist' => true,
                'featured' => false,
                'published' => true,
                'status' => 'open',
            ],
            [
                'title' => 'CourtConnect Community League',
                'slug' => 'courtconnect-community-league',
                'sport' => 'Basketball',
                'event_type' => 'League',
                'description' => 'Season-based league with standings and playoffs.',
                'image' => 'images/event-basketball-league.webp',
                'price' => 1000.00,
                'max_slots' => 64,
                'registered' => 0,
                'start_date' => '2026-09-01',
                'end_date' => '2026-11-30',
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'location' => 'Multi-Sport Arena',
                'court_id' => $courtId,
                'requires_payment' => true,
                'allow_waitlist' => true,
                'featured' => true,
                'published' => true,
                'status' => 'open',
            ],
        ];

        $events = [];
        foreach ($eventsData as $data) {
            $events[] = Event::create($data);
        }

        // Get or Create Mock Users
        $users = [];
        
        // 1. Get default player
        $player = User::where('email', 'player@courtconnect.test')->first();
        if ($player) {
            $users[] = $player;
        }

        // 2. Add some more players
        $extraPlayers = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@courtconnect.test', 'phone' => '+63 911 333 4444'],
            ['name' => 'Maria Santos', 'email' => 'maria@courtconnect.test', 'phone' => '+63 911 444 5555'],
            ['name' => 'Mika Santos', 'email' => 'mika@courtconnect.test', 'phone' => '+63 911 555 6666'],
            ['name' => 'Jon Lim', 'email' => 'jon@courtconnect.test', 'phone' => '+63 911 666 7777'],
        ];

        foreach ($extraPlayers as $ep) {
            $users[] = User::updateOrCreate(
                ['email' => $ep['email']],
                [
                    'name' => $ep['name'],
                    'phone' => $ep['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Seed registrations for each event
        foreach ($events as $event) {
            // Register a subset of users
            $userSubset = array_slice($users, 0, rand(2, count($users)));

            foreach ($userSubset as $index => $u) {
                // Determine payment status/method
                $method = $index % 2 === 0 ? 'credit_card' : 'gcash';
                $payStatus = $method === 'credit_card' ? 'paid' : 'pending_verification';
                $regStatus = $payStatus === 'paid' ? 'confirmed' : 'pending';

                EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => $u->id,
                    'payment_status' => $payStatus,
                    'registration_status' => $regStatus,
                    'payment_method' => $method,
                    'proof_image' => $method === 'gcash' ? 'images/courtconnect-mark.webp' : null, // mock proof image
                    'reference_number' => $method === 'gcash' ? 'GCASH-REF-' . rand(100000, 999999) : null,
                ]);
            }

            // Recalculate and update event's registered spot count
            $event->update([
                'registered' => $event->registrations()->where('registration_status', 'confirmed')->count(),
            ]);
        }
    }
}
