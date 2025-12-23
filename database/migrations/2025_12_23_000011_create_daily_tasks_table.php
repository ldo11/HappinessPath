<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('day_number');
            $table->foreignId('solution_id')->nullable()->constrained('solutions')->nullOnDelete();
            $table->unsignedTinyInteger('difficulty')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['day_number', 'solution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
    }
};
