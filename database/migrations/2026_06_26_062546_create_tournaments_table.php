<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['single', 'double', 'round_robin']);
            $table->enum('status', [
                'draft', 'published', 'registration_open', 'registration_closed',
                'seeding', 'scheduled', 'active', 'paused', 'completed', 'cancelled', 'archived'
            ])->default('draft');
            $table->dateTime('registration_start');
            $table->dateTime('registration_end');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->integer('max_participants')->default(16);
            $table->decimal('entry_fee', 10, 2)->default(0);
            $table->boolean('auto_schedule')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
