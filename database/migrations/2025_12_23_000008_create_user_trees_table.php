<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_trees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('season', ['spring', 'summer', 'autumn', 'winter'])->default('spring');
            $table->unsignedTinyInteger('health')->default(0);
            $table->unsignedBigInteger('exp')->default(0);
            $table->unsignedBigInteger('fruits_balance')->default(0);
            $table->unsignedBigInteger('total_fruits_given')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('season');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_trees');
    }
};
