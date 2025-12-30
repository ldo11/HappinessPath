<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assessment_answers')) {
            return;
        }

        Schema::table('assessment_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_answers', 'order')) {
                $table->integer('order')->nullable()->after('score');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('assessment_answers')) {
            return;
        }

        Schema::table('assessment_answers', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_answers', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
