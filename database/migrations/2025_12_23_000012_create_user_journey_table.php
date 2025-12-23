<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_journey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('current_day')->default(1);
            $table->enum('custom_focus', ['heart', 'grit', 'wisdom'])->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index(['custom_focus', 'last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_journey');
    }
};
