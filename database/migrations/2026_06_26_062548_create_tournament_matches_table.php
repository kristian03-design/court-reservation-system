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
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->string('bracket_type')->default('winner'); // winner, loser, grand_final
            $table->integer('round_number');
            $table->integer('match_number');
            $table->foreignId('participant1_id')->nullable()->constrained('tournament_participants')->nullOnDelete();
            $table->foreignId('participant2_id')->nullable()->constrained('tournament_participants')->nullOnDelete();
            $table->foreignId('winner_id')->nullable()->constrained('tournament_participants')->nullOnDelete();
            $table->foreignId('loser_id')->nullable()->constrained('tournament_participants')->nullOnDelete();
            $table->foreignId('next_match_id')->nullable()->constrained('tournament_matches')->nullOnDelete();
            $table->foreignId('loser_next_match_id')->nullable()->constrained('tournament_matches')->nullOnDelete();
            $table->enum('status', [
                'waiting', 'ready', 'scheduled', 'check_in', 'live',
                'finished', 'walkover', 'cancelled', 'forfeit', 'bye'
            ])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
