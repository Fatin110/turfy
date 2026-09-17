<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('rules')->nullable();
            $table->string('sport');
            $table->string('format')->default('knockout');
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('banner_image')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->dateTime('registration_deadline')->nullable();
            $table->unsignedInteger('max_teams')->default(16);
            $table->decimal('entry_fee', 10, 2)->default(0);
            $table->string('prize_info')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['status', 'sport']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
