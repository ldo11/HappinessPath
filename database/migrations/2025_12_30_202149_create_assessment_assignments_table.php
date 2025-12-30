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
        Schema::create('assessment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_thread_id')->constrained('consultation_threads')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // Consultant who assigned
            $table->string('access_token', 64)->unique(); // For signed URL access
            $table->timestamp('expires_at')->nullable(); // Optional expiration for access
            $table->timestamps();
            
            $table->index('consultation_thread_id');
            $table->index('assessment_id');
            $table->index('user_id');
            $table->index('access_token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_assignments');
    }
};
