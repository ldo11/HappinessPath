<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'pillar_tag')) {
                $table->enum('pillar_tag', ['body', 'mind', 'wisdom'])->nullable()->after('url');
            }
            if (!Schema::hasColumn('videos', 'source_tag')) {
                $table->enum('source_tag', ['buddhism', 'christianity', 'science'])->default('science')->after('pillar_tag');
            }
            if (!Schema::hasColumn('videos', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('source_tag');
            }
        });

        if (Schema::hasColumn('videos', 'category') && Schema::hasColumn('videos', 'pillar_tag')) {
            DB::table('videos')
                ->whereNull('pillar_tag')
                ->update([
                    'pillar_tag' => DB::raw('category'),
                ]);
        }

        DB::table('videos')
            ->whereNull('pillar_tag')
            ->update([
                'pillar_tag' => 'mind',
            ]);
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('videos', 'source_tag')) {
                $table->dropColumn('source_tag');
            }
            if (Schema::hasColumn('videos', 'pillar_tag')) {
                $table->dropColumn('pillar_tag');
            }
        });
    }
};
