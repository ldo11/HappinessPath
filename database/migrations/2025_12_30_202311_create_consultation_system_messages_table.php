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
        Schema::create('consultation_system_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('consultation_threads')->onDelete('cascade');
            $table->text('content');
            $table->string('type')->default('system_notification'); // assessment_assignment, system_notification
            $table->json('metadata')->nullable(); // Store additional data like assessment_id, assignment_id, etc.
            $table->timestamps();
            
            $table->index('thread_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_system_messages');
    }
};
