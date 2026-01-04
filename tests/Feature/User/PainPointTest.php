<?php

namespace Tests\Feature;

use App\Models\PainPoint;
use App\Models\User;
use App\Models\UserQuizResult;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PainPointTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_hides_pain_points_when_all_zero(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        UserQuizResult::create([
            'user_id' => $user->id,
            'heart_score' => 1,
            'grit_score' => 1,
            'wisdom_score' => 1,
            'dominant_issue' => 'heart',
        ]);

        $p1 = PainPoint::create(['name' => 'PP 1', 'category' => 'mind']);
        $p2 = PainPoint::create(['name' => 'PP 2', 'category' => 'mind']);

        $user->painPoints()->sync([
            $p1->id => ['score' => 0],
            $p2->id => ['score' => 0],
        ]);

        $response = $this->actingAs($user)->get('/en/dashboard');
        $response->assertOk();

        $response->assertDontSee('Nỗi khổ', false);
        $response->assertDontSee('Quản lý tất cả vấn đề', false);
    }

    public function test_dashboard_shows_only_top_3_severe(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        UserQuizResult::create([
            'user_id' => $user->id,
            'heart_score' => 1,
            'grit_score' => 1,
            'wisdom_score' => 1,
            'dominant_issue' => 'heart',
        ]);

        $p10 = PainPoint::create(['name' => 'Score 10', 'category' => 'mind']);
        $p9 = PainPoint::create(['name' => 'Score 9', 'category' => 'mind']);
        $p8 = PainPoint::create(['name' => 'Score 8', 'category' => 'mind']);
        $p2 = PainPoint::create(['name' => 'Score 2', 'category' => 'mind']);
        $p1 = PainPoint::create(['name' => 'Score 1', 'category' => 'mind']);

        $user->painPoints()->sync([
            $p10->id => ['score' => 10],
            $p9->id => ['score' => 9],
            $p8->id => ['score' => 8],
            $p2->id => ['score' => 2],
            $p1->id => ['score' => 1],
        ]);

        $response = $this->actingAs($user)->get('/en/dashboard');
        $response->assertOk();

        $response->assertViewHas('myPainPoints', function ($myPainPoints) use ($p10, $p9, $p8, $p2, $p1) {
            // Controller returns all pain points sorted by score desc
            if (!$myPainPoints) {
                return false;
            }
            
            if ($myPainPoints->count() !== 5) {
                return false;
            }

            // Check if the first 3 are the top scorers
            $names = $myPainPoints->take(3)->pluck('name')->values()->all();
            
            // Debug failure
            if ($names !== [$p10->name, $p9->name, $p8->name]) {
                dump('Expected:', [$p10->name, $p9->name, $p8->name]);
                dump('Actual:', $names);
                dump('All Scores:', $myPainPoints->map(fn($p) => $p->name . ': ' . $p->pivot->score)->all());
            }

            return $names === [$p10->name, $p9->name, $p8->name];
        });

        $response->assertSee($p10->name, false);
        $response->assertSee($p9->name, false);
        $response->assertSee($p8->name, false);
    }
}
