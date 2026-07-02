<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sport');
            $table->string('event_type');
            $table->text('description');
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('max_slots')->default(0);
            $table->integer('registered')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->foreignId('court_id')->nullable()->constrained('courts')->nullOnDelete();
            $table->boolean('requires_payment')->default(false);
            $table->boolean('allow_waitlist')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('published')->default(false);
            $table->string('status')->default('draft'); // draft, open, closed, completed, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
