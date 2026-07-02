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
        Schema::table('tournament_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('tournament_participants', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('tournament_participants', 'wins')) {
                $table->integer('wins')->default(0);
            }
            if (!Schema::hasColumn('tournament_participants', 'losses')) {
                $table->integer('losses')->default(0);
            }
            if (!Schema::hasColumn('tournament_participants', 'matches_played')) {
                $table->integer('matches_played')->default(0);
            }
            if (!Schema::hasColumn('tournament_participants', 'is_eliminated')) {
                $table->boolean('is_eliminated')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_participants', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'wins', 'losses', 'matches_played', 'is_eliminated']);
        });
    }
};
