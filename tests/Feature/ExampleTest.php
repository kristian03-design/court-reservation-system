<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_customer_can_view_their_reservation_confirmation(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $court = Court::create([
            'court_name' => 'Test Court',
            'court_type' => 'Basketball',
            'description' => 'A court for tests.',
            'capacity' => 10,
            'hourly_rate' => 500,
            'status' => 'available',
        ]);

        $reservation = Reservation::create([
            'reservation_number' => 'CC-TEST-001',
            'user_id' => $user->id,
            'court_id' => $court->id,
            'reservation_date' => now()->addDay()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '19:00',
            'players' => 4,
            'total_amount' => 500,
            'status' => 'pending_payment',
        ]);

        $reservation->payment()->create([
            'amount' => 500,
            'payment_method' => 'pay_at_venue',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($user)
            ->get(route('reservations.show', $reservation))
            ->assertOk()
            ->assertSee('CC-TEST-001');
    }

    public function test_customer_can_book_multiple_hours_and_calculate_correct_total(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $court = Court::create([
            'court_name' => 'Swift Badminton Hall',
            'court_type' => 'Badminton',
            'description' => 'A badminton hall.',
            'capacity' => 4,
            'hourly_rate' => 420,
            'status' => 'available',
        ]);

        $this->actingAs($user)
            ->post(route('booking.store'), [
                'court_id' => $court->id,
                'reservation_date' => now()->addDay()->toDateString(),
                'start_time' => '16:00',
                'end_time' => '18:00', // 2 Hours
                'players' => 2,
                'payment_method' => 'pay_at_venue',
                'notes' => 'Looking forward to playing!',
            ])
            ->assertRedirect();

        $reservation = Reservation::where('user_id', $user->id)->first();
        $this->assertNotNull($reservation);
        // Hourly rate (420) * 2 hours = 840
        $this->assertEquals(840.0, (float)$reservation->total_amount);
    }

    public function test_admin_login_page_is_separate(): void
    {
        $this->withoutVite();

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Admin Login')
            ->assertSee('Admin Portal');
    }

    public function test_admin_can_login_from_admin_login_page(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.otp'));

        $this->assertGuest();

        $otp = session('admin_otp_code');

        $this->post(route('admin.otp.verify'), [
            'otp' => $otp,
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_login_ignores_customer_dashboard_intended_url(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'email' => 'intended-admin@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->withSession(['url.intended' => route('dashboard')])
            ->post(route('admin.login.store'), [
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('admin.otp'));

        $this->post(route('admin.otp.verify'), [
            'otp' => session('admin_otp_code'),
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_cannot_access_customer_dashboard(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_still_view_public_guest_pages(): void
    {
        $this->withoutVite();

        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $court = Court::create([
            'court_name' => 'Public Court',
            'court_type' => 'Basketball',
            'description' => 'A court visible to everyone.',
            'capacity' => 10,
            'hourly_rate' => 600,
            'status' => 'available',
        ]);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Spaces that feel');

        $this->actingAs($admin)
            ->get(route('courts.index'))
            ->assertOk()
            ->assertSee('Find Your Court.');

        $this->actingAs($admin)
            ->get(route('courts.show', $court))
            ->assertOk()
            ->assertSee('Public Court');
    }

    public function test_admin_cannot_login_from_customer_login_page(): void
    {
        $admin = User::factory()->create([
            'email' => 'courtconnect2026@gmail.com',
            'password' => Hash::make('courtconnect2026'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $admin->email,
                'password' => 'courtconnect2026',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_customer_cannot_login_from_admin_login_page(): void
    {
        Mail::fake();

        $customer = User::factory()->create([
            'email' => 'customer@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => $customer->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_customer_login_redirects_directly_to_dashboard_bypassing_intended_url(): void
    {
        $this->withoutVite();

        $customer = User::factory()->create([
            'email' => 'customer-redirect@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->withSession(['url.intended' => route('home')])
            ->post(route('login'), [
                'email' => $customer->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_otp_rejects_wrong_code(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'email' => 'admin-otp@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.otp'));

        $this->from(route('admin.otp'))
            ->post(route('admin.otp.verify'), [
                'otp' => '000000',
            ])
            ->assertRedirect(route('admin.otp'));

        $this->assertGuest();
    }

    public function test_user_can_edit_profile(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Edit Profile');

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.test',
                'phone' => '+63 999 999 9999',
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@example.test', $user->email);
        $this->assertEquals('+63 999 999 9999', $user->phone);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'profile_updated',
        ]);
    }

    public function test_admin_can_manage_court_schedules(): void
    {
        $this->withoutVite();

        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $court = Court::create([
            'court_name' => 'Basketball Pro',
            'court_type' => 'Basketball',
            'description' => 'Fine court.',
            'capacity' => 10,
            'hourly_rate' => 600,
            'status' => 'available',
        ]);

        // Get schedules index
        $this->actingAs($admin, 'admin')
            ->get(route('admin.courts.schedules.index', $court))
            ->assertOk()
            ->assertSee('Schedules');

        // Create a schedule block
        $this->actingAs($admin, 'admin')
            ->post(route('admin.courts.schedules.store', $court), [
                'schedule_date' => now()->addDay()->toDateString(),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'availability_status' => 'maintenance',
                'reason' => 'Flooring buffing',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('court_schedules', [
            'court_id' => $court->id,
            'start_time' => '10:00',
            'availability_status' => 'maintenance',
        ]);

        // Prevent overlap block creation
        $this->actingAs($admin, 'admin')
            ->from(route('admin.courts.schedules.index', $court))
            ->post(route('admin.courts.schedules.store', $court), [
                'schedule_date' => now()->addDay()->toDateString(),
                'start_time' => '11:00',
                'end_time' => '13:00',
                'availability_status' => 'closed',
                'reason' => 'Another buffer',
            ])
            ->assertRedirect(route('admin.courts.schedules.index', $court))
            ->assertSessionHasErrors(['start_time']);

        $sched = $court->schedules()->first();

        // Delete a schedule block
        $this->actingAs($admin, 'admin')
            ->delete(route('admin.courts.schedules.destroy', [$court, $sched]))
            ->assertRedirect();

        $this->assertDatabaseMissing('court_schedules', [
            'id' => $sched->id,
        ]);
    }

    public function test_new_guest_pages_render_successfully(): void
    {
        $this->withoutVite();

        // Create a court to ensure availability page renders slots
        Court::create([
            'court_name' => 'Availability Test Court',
            'court_type' => 'Basketball',
            'description' => 'Test court.',
            'capacity' => 10,
            'hourly_rate' => 500,
            'status' => 'available',
        ]);

        // Create a tournament to ensure tournaments page renders it
        \App\Models\Tournament::create([
            'name' => 'Summer Smash Open',
            'description' => 'Annual Summer Smash Open tournament.',
            'type' => 'single',
            'status' => 'published',
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(5),
            'start_date' => now()->addDays(10),
            'max_participants' => 32,
            'entry_fee' => 150.00,
            'auto_schedule' => true,
        ]);

        // 1. Tournaments Page
        $this->get(route('tournaments'))
            ->assertOk()
            ->assertSee('TEST YOUR LIMITS')
            ->assertSee('Summer Smash Open');

        // Create an event to ensure events page renders it
        \App\Models\Event::create([
            'title' => 'Weekend Social Mixer',
            'slug' => 'weekend-social-mixer',
            'sport' => 'Social Play',
            'event_type' => 'Weekly Session',
            'description' => 'Show up solo or with friends.',
            'image' => 'images/courtconnect-multisport-hero.png',
            'price' => 150.00,
            'max_slots' => 48,
            'registered' => 0,
            'start_date' => now()->next('Saturday')->toDateString(),
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
            'location' => 'CourtConnect Main Arena',
            'requires_payment' => true,
            'allow_waitlist' => true,
            'featured' => true,
            'published' => true,
            'status' => 'open',
        ]);

        // 2. Events Page
        $this->get(route('events'))
            ->assertOk()
            ->assertSee('BEYOND THE GAME')
            ->assertSee('Weekend Social Mixer');

        // 3. Availability Page
        $this->get(route('availability'))
            ->assertOk()
            ->assertSee('FIND AN OPEN COURT')
            ->assertSee('Daily time slots');
    }

    public function test_concurrent_sessions_customer_and_admin_remain_logged_in(): void
    {
        Mail::fake();
        $this->withoutVite();

        // 1. Create a customer and an admin
        $customer = User::factory()->create([
            'email' => 'customer@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Log in as a customer under the web guard
        $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($customer, 'web');
        $this->assertGuest('admin');

        // 3. In the same session, log in as an admin under the admin guard
        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.otp'));

        $otp = session('admin_otp_code');
        $this->post(route('admin.otp.verify'), [
            'otp' => $otp,
        ])->assertRedirect(route('admin.dashboard'));

        // 4. Assert that BOTH guards are authenticated concurrently
        $this->assertAuthenticatedAs($customer, 'web');
        $this->assertAuthenticatedAs($admin, 'admin');

        // 5. Log out of admin
        $this->delete(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        // 6. Assert that admin is guest but customer is STILL authenticated
        $this->assertGuest('admin');
        $this->assertAuthenticatedAs($customer, 'web');

        // 7. Log out of customer
        $this->delete(route('logout'))
            ->assertRedirect(route('home'));

        // 8. Assert that customer is now guest too
        $this->assertGuest('web');
        $this->assertGuest('admin');
    }

    public function test_admin_can_filter_reservations_by_court_and_date(): void
    {
        $this->withoutVite();

        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $court1 = Court::create([
            'court_name' => 'Court One',
            'court_type' => 'Basketball',
            'capacity' => 10,
            'hourly_rate' => 500,
            'status' => 'available',
        ]);

        $court2 = Court::create([
            'court_name' => 'Court Two',
            'court_type' => 'Volleyball',
            'capacity' => 12,
            'hourly_rate' => 600,
            'status' => 'available',
        ]);

        $res1 = Reservation::create([
            'reservation_number' => 'CC-FILTER-001',
            'user_id' => $admin->id,
            'court_id' => $court1->id,
            'reservation_date' => '2026-07-01',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'players' => 4,
            'total_amount' => 1000,
            'status' => 'pending_payment',
        ]);

        $res2 = Reservation::create([
            'reservation_number' => 'CC-FILTER-002',
            'user_id' => $admin->id,
            'court_id' => $court2->id,
            'reservation_date' => '2026-07-02',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'players' => 4,
            'total_amount' => 1200,
            'status' => 'confirmed',
        ]);

        // 1. Filter by court1
        $this->actingAs($admin, 'admin')
            ->get(route('admin.reservations.index', ['court_id' => $court1->id]))
            ->assertOk()
            ->assertSee('CC-FILTER-001')
            ->assertDontSee('CC-FILTER-002');

        // 2. Filter by date '2026-07-02'
        $this->actingAs($admin, 'admin')
            ->get(route('admin.reservations.index', ['date' => '2026-07-02']))
            ->assertOk()
            ->assertSee('CC-FILTER-002')
            ->assertDontSee('CC-FILTER-001');
    }
}
