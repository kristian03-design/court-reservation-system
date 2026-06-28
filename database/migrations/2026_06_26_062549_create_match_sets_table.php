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
        Schema::create('match_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('tournament_matches')->cascadeOnDelete();
            $table->integer('set_number');
            $table->integer('participant1_score');
            $table->integer('participant2_score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_sets');
    }
};
