<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('consultation_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->index(['thread_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_replies');
    }
};
