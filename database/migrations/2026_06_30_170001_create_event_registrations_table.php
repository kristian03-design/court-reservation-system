<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('payment_status')->default('unpaid'); // unpaid, pending_verification, paid
            $table->string('registration_status')->default('pending'); // pending, confirmed, waitlisted, cancelled
            $table->string('payment_method')->nullable();
            $table->string('proof_image')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamps();
            
            $table->unique(['event_id', 'user_id']); // Prevent duplicate registrations
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
