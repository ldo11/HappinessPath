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
        Schema::create('user_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->json('answers'); // Store user's selected options
            $table->integer('total_score')->default(0);
            $table->enum('submission_mode', ['self_review', 'submitted_for_consultation'])->default('self_review');
            $table->foreignId('consultation_thread_id')->nullable()->constrained('consultation_threads')->onDelete('set null');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('assessment_id');
            $table->index('submission_mode');
            $table->index('consultation_thread_id');
            
            // Ensure one assessment result per user
            $table->unique(['user_id', 'assessment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assessments');
    }
};
