<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultant_pain_point', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pain_point_id')->constrained('pain_points')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'pain_point_id']);
            $table->index(['user_id']);
            $table->index(['pain_point_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_pain_point');
    }
};
