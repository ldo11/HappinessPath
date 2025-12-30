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
            if (!Schema::hasColumn('videos', 'language')) {
                $table->string('language', 2)->default('vi')->after('url');
            }
            if (!Schema::hasColumn('videos', 'pillar_tags')) {
                $table->json('pillar_tags')->nullable()->after('language');
            }
            if (!Schema::hasColumn('videos', 'source_tags')) {
                $table->json('source_tags')->nullable()->after('pillar_tags');
            }
        });

        if (!Schema::hasTable('videos')) {
            return;
        }

        $videos = DB::table('videos')->select(['id', 'pillar_tag', 'source_tag', 'category'])->get();

        foreach ($videos as $video) {
            $pillar = $video->pillar_tag ?? $video->category ?? null;
            $source = $video->source_tag ?? null;

            $pillarTags = null;
            if (is_string($pillar) && in_array($pillar, ['body', 'mind', 'wisdom'], true)) {
                $pillarTags = [$pillar];
            }

            $sourceTags = null;
            if (is_string($source) && in_array($source, ['buddhism', 'christianity', 'science'], true)) {
                $sourceTags = [$source];
            }

            DB::table('videos')->where('id', $video->id)->update([
                'pillar_tags' => $pillarTags ? json_encode($pillarTags) : null,
                'source_tags' => $sourceTags ? json_encode($sourceTags) : null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'source_tags')) {
                $table->dropColumn('source_tags');
            }
            if (Schema::hasColumn('videos', 'pillar_tags')) {
                $table->dropColumn('pillar_tags');
            }
            if (Schema::hasColumn('videos', 'language')) {
                $table->dropColumn('language');
            }
        });
    }
};
