<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_sets', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable
            $table->json('description')->nullable(); // Translatable
            $table->string('type')->default('growth'); // e.g., 'Healing', 'Growth'
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('daily_missions', function (Blueprint $table) {
            $table->foreignId('mission_set_id')->nullable()->constrained('mission_sets')->cascadeOnDelete();
            $table->unsignedInteger('day_number')->nullable(); // 1-30
            
            // Adding index for faster lookup during dashboard load
            $table->index(['mission_set_id', 'day_number']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('active_mission_set_id')->nullable()->constrained('mission_sets')->nullOnDelete();
            $table->date('mission_started_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['active_mission_set_id']);
            $table->dropColumn(['active_mission_set_id', 'mission_started_at']);
        });

        Schema::table('daily_missions', function (Blueprint $table) {
            $table->dropForeign(['mission_set_id']);
            $table->dropIndex(['mission_set_id', 'day_number']);
            $table->dropColumn(['mission_set_id', 'day_number']);
        });

        Schema::dropIfExists('mission_sets');
    }
};
