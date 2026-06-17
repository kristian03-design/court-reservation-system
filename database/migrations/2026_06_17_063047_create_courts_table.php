<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('court_name');
            $table->string('court_type');
            $table->text('description')->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('amenities')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
