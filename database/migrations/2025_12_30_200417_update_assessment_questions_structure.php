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
        // Drop existing tables to create new structure
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_questions');
        
        // Create new assessment_questions table
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->json('content');
            $table->enum('type', ['single_choice', 'multi_choice'])->default('single_choice');
            $table->integer('order');
            $table->timestamps();
            
            $table->index('assessment_id');
            $table->index('type');
            $table->index('order');
        });
        
        // Create assessment_options table (renamed from assessment_answers)
        Schema::create('assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('assessment_questions')->onDelete('cascade');
            $table->json('content');
            $table->integer('score');
            $table->timestamps();
            
            $table->index('question_id');
            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_options');
        Schema::dropIfExists('assessment_questions');
    }
};
