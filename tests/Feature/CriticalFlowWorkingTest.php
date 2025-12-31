<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Assessment;
use App\Models\DailyTask;
use App\Models\ConsultationThread;
use App\Models\ConsultationReply;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CriticalFlowWorkingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'language' => 'en', 
            'role' => 'user',
            'email_verified_at' => now()
        ]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function assessment_controller_can_handle_string_id()
    {
        // Arrange
        $assessment = Assessment::factory()->create(['status' => 'active']);
        
        // Act & Assert - Test the controller method directly
        $controller = new \App\Http\Controllers\Web\UserAssessmentController();
        
        // This should not throw an exception with string ID
        $result = $controller->show($assessment->id);
        
        $this->assertNotNull($result);
        $this->assertEquals('web.assessments.show', $result->getName());
    }

    /** @test */
    public function daily_mission_controller_can_handle_start_and_complete()
    {
        // Arrange
        $task = DailyTask::factory()->create(['type' => 'daily_mission', 'status' => 'active']);
        
        // Act & Assert - Test the controller methods directly
        $controller = new \App\Http\Controllers\Web\DailyMissionController();
        
        // Test start method
        $response = $controller->start(\Illuminate\Http\Request::create('/tasks/1/start', 'POST'), $task->id);
        $this->assertEquals(200, $response->status());
        
        // Test completeTask method - create request with authenticated user
        $request = \Illuminate\Http\Request::create('/tasks/1/complete', 'POST', [
            'report_content' => 'Test completion report'
        ]);
        
        // Mock the authenticated user
        auth()->setUser($this->user);
        
        $response = $controller->completeTask($request, $task->id);
        $this->assertEquals(200, $response->status());
    }

    /** @test */
    public function consultation_controller_can_handle_thread_and_reply()
    {
        // Arrange
        $thread = ConsultationThread::create([
            'user_id' => $this->user->id,
            'title' => 'Help me',
            'content' => 'I need help with something',
            'status' => 'open'
        ]);
        
        // Act & Assert - Test the controller methods directly
        $controller = new \App\Http\Controllers\Web\ConsultationController();
        
        // Test show method
        $response = $controller->show(\Illuminate\Http\Request::create('/consultations/1', 'GET'), 'en', $thread);
        $this->assertEquals('consultations.show', $response->getName());
        
        // Test reply method - create request with authenticated user
        $request = \Illuminate\Http\Request::create('/consultations/1/reply', 'POST', [
            'content' => 'New reply message'
        ]);
        
        // Mock the authenticated user
        auth()->setUser($this->user);
        
        // Just test that the method doesn't throw an exception and creates the reply
        try {
            $response = $controller->reply($request, 'en', $thread);
            // Assert reply was created
            $this->assertDatabaseHas('consultation_replies', [
                'content' => 'New reply message',
                'user_id' => $this->user->id
            ]);
            // Assert JSON response
            $this->assertEquals(200, $response->status());
            $this->assertStringContainsString('"success":true', $response->getContent());
        } catch (\Exception $e) {
            $this->fail('Reply method failed: ' . $e->getMessage());
        }
    }
}
