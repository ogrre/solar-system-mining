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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_system_id')->constrained()->onDelete('cascade');
            $table->foreignId('host_user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['waiting', 'active', 'paused', 'completed', 'abandoned'])->default('waiting');
            $table->integer('current_players')->default(0);
            $table->json('game_settings')->nullable();
            $table->json('game_state')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->boolean('is_public')->default(true);
            $table->string('join_code', 8)->unique()->nullable();
            $table->timestamps();

            $table->index(['solar_system_id', 'status']);
            $table->index('host_user_id');
            $table->index('status');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
