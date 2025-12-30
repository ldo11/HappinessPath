<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pain_points', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pain_point_id')->constrained('pain_points')->cascadeOnDelete();
            $table->unsignedTinyInteger('severity');
            $table->timestamps();

            $table->primary(['user_id', 'pain_point_id']);
            $table->index(['user_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pain_points');
    }
};
