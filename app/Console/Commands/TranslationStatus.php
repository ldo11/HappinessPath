<?php

namespace App\Console\Commands;

use App\Models\LanguageLine;
use Illuminate\Console\Command;

class TranslationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:status {--group= : Show status for specific group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show translation status for all languages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Translation Status ===');
        
        $query = LanguageLine::query();
        if ($group = $this->option('group')) {
            $query->where('group', $group);
            $this->info("Showing status for group: {$group}");
        }

        $total = $query->count();
        $complete = $query->where(function($q) {
            return $q->whereNotNull('text->en')
                      ->whereNotNull('text->vi')
                      ->whereNotNull('text->de')
                      ->whereNotNull('text->kr');
        })->count();

        $this->info("Total translations: {$total}");
        $this->info("Complete (EN+VI+DE+KR): {$complete}");
        $this->info("Completion rate: " . round(($complete / $total) * 100, 1) . "%");

        if ($group === 'json') {
            $this->info("\n=== JSON Group Breakdown ===");
            $groups = ['auth', 'dashboard', 'assessment', 'navigation', 'common'];
            foreach ($groups as $g) {
                $count = LanguageLine::where('group', 'json')->where('key', 'like', $g . '.%')->count();
                $complete = LanguageLine::where('group', 'json')
                    ->where('key', 'like', $g . '.%')
                    ->where(function($q) {
                        return $q->whereNotNull('text->en')
                                  ->whereNotNull('text->vi')
                                  ->whereNotNull('text->de')
                                  ->whereNotNull('text->kr');
                    })->count();
                $this->info("{$g}: {$complete}/{$count} complete");
            }
        }

        return 0;
    }
}
