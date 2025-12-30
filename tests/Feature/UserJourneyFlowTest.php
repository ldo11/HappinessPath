<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\PainPoint;
use App\Models\User;
use App\Models\UserQuizResult;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserJourneyFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_assessment_and_resubmit_with_new_scores(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        AssessmentQuestion::create([
            'pillar_group' => 'heart',
            'order' => 1,
            'content' => ['en' => 'Heart Q', 'vi' => 'Câu hỏi Heart'],
        ]);
        AssessmentQuestion::create([
            'pillar_group' => 'grit',
            'order' => 2,
            'content' => ['en' => 'Grit Q', 'vi' => 'Câu hỏi Grit'],
        ]);
        AssessmentQuestion::create([
            'pillar_group' => 'wisdom',
            'order' => 3,
            'content' => ['en' => 'Wisdom Q', 'vi' => 'Câu hỏi Wisdom'],
        ]);

        $heartQ = AssessmentQuestion::where('pillar_group', 'heart')->firstOrFail();
        $gritQ = AssessmentQuestion::where('pillar_group', 'grit')->firstOrFail();
        $wisdomQ = AssessmentQuestion::where('pillar_group', 'wisdom')->firstOrFail();

        $response1 = $this->actingAs($user)->post('/en/assessment/submit', [
            'answers' => [
                (string) $heartQ->id => 1,
                (string) $gritQ->id => 2,
                (string) $wisdomQ->id => 3,
            ],
        ]);

        $response1->assertRedirect('/en/dashboard');

        $result1 = UserQuizResult::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(1, (int) $result1->heart_score);
        $this->assertSame(2, (int) $result1->grit_score);
        $this->assertSame(3, (int) $result1->wisdom_score);

        $response2 = $this->actingAs($user)->post('/en/assessment/submit', [
            'answers' => [
                (string) $heartQ->id => 5,
                (string) $gritQ->id => 4,
                (string) $wisdomQ->id => 1,
            ],
        ]);

        $response2->assertRedirect('/en/dashboard');

        $result2 = UserQuizResult::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(5, (int) $result2->heart_score);
        $this->assertSame(4, (int) $result2->grit_score);
        $this->assertSame(1, (int) $result2->wisdom_score);

        $this->assertNotSame($result1->dominant_issue, $result2->dominant_issue);
    }

    public function test_user_can_select_multiple_pain_points_and_save_from_dashboard(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        $pain1 = PainPoint::create([
            'name' => 'Anxiety',
            'category' => 'mind',
            'icon' => 'fa-face-sad-tear',
            'description' => 'Feeling anxious',
        ]);
        $pain2 = PainPoint::create([
            'name' => 'Burnout',
            'category' => 'body',
            'icon' => 'fa-battery-quarter',
            'description' => 'Feeling burned out',
        ]);

        $dashboard = $this->actingAs($user)->get('/en/dashboard');
        $dashboard->assertOk();

        $save = $this->actingAs($user)->post('/en/pain-points', [
            'pain_points' => [
                $pain1->id => ['id' => $pain1->id, 'severity' => 7],
                $pain2->id => ['id' => $pain2->id, 'severity' => 4],
            ],
        ]);

        $save->assertRedirect('/en/pain-points');

        $this->assertDatabaseHas('user_pain_points', [
            'user_id' => $user->id,
            'pain_point_id' => $pain1->id,
            'severity' => 7,
        ]);
        $this->assertDatabaseHas('user_pain_points', [
            'user_id' => $user->id,
            'pain_point_id' => $pain2->id,
            'severity' => 4,
        ]);

        $dashboard2 = $this->actingAs($user)->get('/en/dashboard');
        $dashboard2->assertOk();
        $dashboard2->assertSee((string) $pain1->id, false);
        $dashboard2->assertSee((string) $pain2->id, false);
    }

    public function test_tree_icon_changes_based_on_user_age(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        $user->dob = now()->subYears(10)->toDateString();
        $user->save();

        $young = $this->actingAs($user)->get('/en/dashboard');
        $young->assertOk();
        $young->assertSee('fa-seedling', false);

        $user->dob = now()->subYears(60)->toDateString();
        $user->save();

        $old = $this->actingAs($user)->get('/en/dashboard');
        $old->assertOk();
        $old->assertSee('fa-tree', false);
    }
}
