<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pain_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['mind', 'wisdom', 'body']);
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->string('icon_url')->nullable();
            $table->timestamps();

            $table->unique('name');
            $table->index('category');
        });

        Schema::create('user_pain_points', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pain_point_id')->constrained('pain_points')->cascadeOnDelete();
            $table->unsignedTinyInteger('severity');
            $table->timestamps();

            $table->primary(['user_id', 'pain_point_id']);
            $table->index(['user_id', 'severity']);
        });

        Schema::create('consultant_pain_point', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pain_point_id')->constrained('pain_points')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'pain_point_id']);
            $table->index('user_id');
            $table->index('pain_point_id');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->enum('type', ['sharing', 'advice', 'listening'])->default('sharing');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['receiver_id', 'read_at']);
            $table->index(['sender_id', 'created_at']);
        });

        Schema::create('consultation_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');

            $table->foreignId('related_pain_point_id')->nullable()->constrained('pain_points')->nullOnDelete();
            $table->unsignedBigInteger('related_assessment_id')->nullable();
            $table->foreignId('pain_point_id')->nullable()->constrained('pain_points')->nullOnDelete();
            $table->foreignId('assigned_consultant_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->boolean('is_private')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('user_id');
            $table->index('related_assessment_id');
        });

        Schema::create('consultation_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('consultation_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->index('thread_id');
        });

        Schema::create('consultation_system_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('consultation_threads')->cascadeOnDelete();
            $table->text('content');
            $table->string('type')->default('system_notification');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('thread_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_system_messages');
        Schema::dropIfExists('consultation_replies');
        Schema::dropIfExists('consultation_threads');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('consultant_pain_point');
        Schema::dropIfExists('user_pain_points');
        Schema::dropIfExists('pain_points');
    }
};
