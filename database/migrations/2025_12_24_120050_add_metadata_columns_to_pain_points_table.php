<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pain_points', function (Blueprint $table) {
            if (!Schema::hasColumn('pain_points', 'category')) {
                $table->enum('category', ['mind', 'wisdom', 'body'])->after('name');
            }
            if (!Schema::hasColumn('pain_points', 'icon')) {
                $table->string('icon')->nullable()->after('category');
            }
            if (!Schema::hasColumn('pain_points', 'description')) {
                $table->text('description')->nullable()->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pain_points', function (Blueprint $table) {
            if (Schema::hasColumn('pain_points', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('pain_points', 'icon')) {
                $table->dropColumn('icon');
            }
            if (Schema::hasColumn('pain_points', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
