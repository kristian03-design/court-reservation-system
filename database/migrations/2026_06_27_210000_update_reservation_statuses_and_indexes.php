<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status']);
        });

        // Migrate statuses in reservations table
        DB::table('reservations')->where('status', 'pending')->update(['status' => 'pending_payment']);
        DB::table('reservations')->where('status', 'under_review')->update(['status' => 'pending_payment']);
        DB::table('reservations')->where('status', 'approved')->update(['status' => 'confirmed']);
        DB::table('reservations')->where('status', 'rejected')->update(['status' => 'cancelled']);
    }

    public function down(): void
    {
        DB::table('reservations')->where('status', 'pending_payment')->update(['status' => 'pending']);
        DB::table('reservations')->where('status', 'confirmed')->update(['status' => 'approved']);

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'status']);
        });
    }
};
