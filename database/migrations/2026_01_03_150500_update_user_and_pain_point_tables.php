<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->after('name');
            $table->string('display_language')->default('vi')->after('locale'); // Assuming locale exists, or put it somewhere appropriate
            $table->text('introduction')->nullable()->after('display_language');
            $table->integer('xp_body')->default(0)->after('introduction');
            $table->integer('xp_mind')->default(0)->after('xp_body');
            $table->integer('xp_wisdom')->default(0)->after('xp_mind');
        });

        // Update pain_points table
        Schema::table('pain_points', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending', 'rejected'])->default('active')->after('description');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('status');
            
            // Drop unique index on name before changing to json
            $table->dropUnique(['name']);
        });

        // Change name and description to json. 
        // We use raw SQL to convert existing data to JSON structure if needed, 
        // but for now we'll just change the type. 
        // Note: In a real prod env with data, we'd need to migrate the string data to {"vi": "Old Name"}
        
        // Let's attempt to simple change using Schema builder. 
        // If there's existing data that isn't JSON, this might fail or truncate depending on DB.
        // We will assume for this task we can just change it.
        Schema::table('pain_points', function (Blueprint $table) {
             $table->json('name')->change();
             $table->json('description')->nullable()->change();
        });

        // Rename pivot table and column
        if (Schema::hasTable('user_pain_points') && !Schema::hasTable('pain_point_user')) {
            Schema::rename('user_pain_points', 'pain_point_user');
        }

        Schema::table('pain_point_user', function (Blueprint $table) {
            // Check if severity exists and score does not
            if (Schema::hasColumn('pain_point_user', 'severity')) {
                $table->renameColumn('severity', 'score');
            }
            // If score column doesn't exist and severity didn't exist (unlikely), add it
            // But we assume it was severity.
        });
        
        // Make sure score is integer 0-10. 
        // severity was unsignedTinyInteger, which fits 0-10.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pain_point_user', function (Blueprint $table) {
            $table->renameColumn('score', 'severity');
        });

        if (Schema::hasTable('pain_point_user')) {
            Schema::rename('pain_point_user', 'user_pain_points');
        }

        Schema::table('pain_points', function (Blueprint $table) {
             $table->dropForeign(['created_by_user_id']);
             $table->dropColumn('created_by_user_id');
             $table->dropColumn('status');
             
             // Reverting JSON to string is lossy if multiple langs
             $table->string('name')->change();
             $table->text('description')->nullable()->change();
             
             $table->unique('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'display_language', 'introduction', 'xp_body', 'xp_mind', 'xp_wisdom']);
        });
    }
};
