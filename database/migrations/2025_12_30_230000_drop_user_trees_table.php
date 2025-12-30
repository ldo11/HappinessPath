<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the user_trees table
        Schema::dropIfExists('user_trees');
    }

    public function down(): void
    {
        // Recreate the user_trees table if we need to rollback
        Schema::create('user_trees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('season', ['spring', 'summer', 'autumn', 'winter'])->default('spring');
            $table->integer('health')->default(50);
            $table->integer('exp')->default(0);
            $table->integer('fruits_balance')->default(0);
            $table->integer('total_fruits_given')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
        });
    }
};
