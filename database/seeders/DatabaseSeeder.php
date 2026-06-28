<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\FacilitySetting;
use App\Models\Reservation;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@courtconnect.test'],
            [
                'name' => 'CourtConnect Admin',
                'phone' => '+63 900 000 0000',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'courtconnect2026@gmail.com'],
            [
                'name' => 'CourtConnect 2026',
                'phone' => '+63 900 000 2026',
                'password' => Hash::make('courtconnect2026'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'player@courtconnect.test'],
            [
                'name' => 'Alex Rivera',
                'phone' => '+63 911 222 3333',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $courts = collect([
            ['court_name' => 'Peak Basketball Court', 'court_type' => 'Basketball', 'capacity' => 10, 'hourly_rate' => 950, 'image' => '/images/courtconnect-basketball-court.png'],
            ['court_name' => 'Velocity Volleyball Arena', 'court_type' => 'Volleyball', 'capacity' => 12, 'hourly_rate' => 850, 'image' => '/images/courtconnect-volleyball-court.png'],
            ['court_name' => 'Swift Badminton Hall', 'court_type' => 'Badminton', 'capacity' => 4, 'hourly_rate' => 420, 'image' => '/images/courtconnect-badminton-court.png'],
            ['court_name' => 'Baseline Tennis Court', 'court_type' => 'Tennis', 'capacity' => 4, 'hourly_rate' => 720, 'image' => '/images/courtconnect-tennis-court.png'],
            ['court_name' => 'Urban Futsal Pitch', 'court_type' => 'Futsal', 'capacity' => 10, 'hourly_rate' => 1100, 'image' => '/images/courtconnect-futsal-pitch.png'],
        ])->map(function (array $court) {
            return Court::updateOrCreate(
                ['court_name' => $court['court_name']],
                $court + [
                    'description' => 'Premium indoor court with professional lighting, safe flooring, spectator seating, and clean changing areas.',
                    'amenities' => ['Locker room', 'LED lighting', 'Shower area', 'Scoreboard'],
                    'status' => 'available',
                ]
            );
        });

        $reservation = Reservation::updateOrCreate(
            ['reservation_number' => 'CC-DEMO-001'],
            [
                'user_id' => $customer->id,
                'court_id' => $courts->first()->id,
                'reservation_date' => now()->addDay()->toDateString(),
                'start_time' => '18:00',
                'end_time' => '20:00',
                'players' => 8,
                'total_amount' => 1900,
                'status' => 'approved',
            ]
        );

        $reservation->payment()->updateOrCreate(
            ['reservation_id' => $reservation->id],
            ['amount' => 1900, 'payment_method' => 'pay_at_venue', 'payment_status' => 'unpaid']
        );

        foreach ([
            ['name' => 'Mika Santos', 'rating' => 5, 'comment' => 'CourtConnect made our weekly games effortless. The calendar is clear and approvals are quick.'],
            ['name' => 'Jon Lim', 'rating' => 5, 'comment' => 'The court pages feel polished, and I can see pricing and availability before I commit.'],
            ['name' => 'Facility Manager', 'rating' => 5, 'comment' => 'The admin dashboard gives our team a fast view of pending bookings and revenue.'],
        ] as $testimonial) {
            Testimonial::updateOrCreate(['name' => $testimonial['name']], $testimonial + ['is_featured' => true]);
        }

        foreach ([
            'facility_name' => 'CourtConnect Sports Center',
            'contact_email' => 'hello@courtconnect.test',
            'contact_phone' => '+63 900 123 4567',
            'operating_hours' => 'Daily, 8:00 AM - 10:00 PM',
            'reservation_rules' => 'Reservations require approval. Cancellations are accepted before the reserved schedule.',
        ] as $key => $value) {
            FacilitySetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
