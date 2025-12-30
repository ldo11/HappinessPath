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
        if (Schema::hasTable('daily_tasks')) {
            return;
        }

        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('day');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['mindfulness', 'physical', 'emotional'])->default('mindfulness');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->integer('estimated_minutes')->default(10);
            $table->foreignId('solution_id')->nullable()->constrained()->onDelete('set null');
            $table->json('instructions')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['day', 'status']);
            $table->index('type');
            $table->index('difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
    }
};
