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
            'status' => 'pending',
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

        $this->assertAuthenticatedAs($admin);
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
}
