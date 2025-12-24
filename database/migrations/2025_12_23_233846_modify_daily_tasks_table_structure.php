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
        Schema::table('daily_tasks', function (Blueprint $table) {
            // Rename day_number to day if it exists
            if (Schema::hasColumn('daily_tasks', 'day_number')) {
                $table->renameColumn('day_number', 'day');
            }
            
            // Add missing columns only if they don't exist
            if (!Schema::hasColumn('daily_tasks', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('daily_tasks', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('daily_tasks', 'type')) {
                $table->string('type')->default('mindfulness');
            }
            if (!Schema::hasColumn('daily_tasks', 'estimated_minutes')) {
                $table->integer('estimated_minutes')->default(10);
            }
            if (!Schema::hasColumn('daily_tasks', 'instructions')) {
                $table->json('instructions')->nullable();
            }
            if (!Schema::hasColumn('daily_tasks', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('daily_tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_tasks', function (Blueprint $table) {
            //
        });
    }
};
