<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'customer',
        ]);

        $court = Court::create([
            'court_name' => 'Test Court',
            'court_type' => 'Tennis',
            'capacity' => 4,
            'hourly_rate' => 500,
            'status' => 'available',
        ]);

        $this->event = Event::create([
            'title' => 'Test Mixer',
            'slug' => 'test-mixer',
            'sport' => 'Tennis',
            'event_type' => 'Weekly Session',
            'description' => 'Casual match play for all skill levels.',
            'price' => 0.00,
            'max_slots' => 10,
            'registered' => 0,
            'start_date' => now()->addDays(5)->toDateString(),
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'location' => 'CourtConnect Arena',
            'court_id' => $court->id,
            'requires_payment' => false,
            'allow_waitlist' => true,
            'featured' => false,
            'published' => true,
            'status' => 'open',
        ]);
    }

    public function test_user_can_view_events_list(): void
    {
        $response = $this->get(route('events'));
        $response->assertStatus(200);
        $response->assertSee('Test Mixer');
    }

    public function test_user_can_view_event_details(): void
    {
        $response = $this->get(route('events.show', $this->event->slug));
        $response->assertStatus(200);
        $response->assertSee('Test Mixer');
        $response->assertSee('Casual match play for all skill levels.');
    }

    public function test_guest_is_redirected_when_accessing_checkout(): void
    {
        $response = $this->get(route('events.register', $this->event->slug));
        $response->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_register_for_free_event(): void
    {
        $response = $this->actingAs($this->user)->get(route('events.register', $this->event->slug));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->post(route('events.submit-register', $this->event->slug), [
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Registration Submitted');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'payment_status' => 'paid',
            'registration_status' => 'confirmed',
        ]);

        $this->assertEquals(1, $this->event->fresh()->registered);
    }

    public function test_user_cannot_register_twice(): void
    {
        EventRegistration::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'payment_status' => 'paid',
            'registration_status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->user)->post(route('events.submit-register', $this->event->slug), [
            'payment_method' => 'credit_card',
        ]);

        $response->assertRedirect(route('events.show', $this->event->slug));
    }

    public function test_overbooking_waitlist_logic(): void
    {
        $this->event->update([
            'max_slots' => 1,
            'registered' => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('events.submit-register', $this->event->slug), [
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'registration_status' => 'waitlisted',
        ]);
    }

    public function test_system_notification_is_sent_on_event_publish(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin, 'admin')->post(route('admin.events.store'), [
            'title' => 'Mega Mixer',
            'description' => 'Huge social mixer event.',
            'sport' => 'Tennis',
            'event_type' => 'Weekly Session',
            'price' => 100,
            'max_slots' => 50,
            'start_date' => now()->addDays(5)->toDateString(),
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'location' => 'Main court',
            'status' => 'open',
            'published' => true,
        ]);

        $response->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'title' => 'New Event: Mega Mixer',
        ]);
    }
}
