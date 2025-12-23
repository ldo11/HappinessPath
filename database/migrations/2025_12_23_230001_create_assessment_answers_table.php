<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->json('content');
            $table->integer('score'); // 1, 3, or 5 points
            $table->integer('order');
            $table->timestamps();
            
            $table->index('question_id');
            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
    }
};
