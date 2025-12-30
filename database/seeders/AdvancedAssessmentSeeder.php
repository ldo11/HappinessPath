<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvancedAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a user for created_by
        $user = User::first() ?? User::factory()->create();

        // Create the first assessment: "Tìm hiểu chính mình" (Understanding Yourself)
        $assessment = Assessment::create([
            'title' => [
                'vi' => 'Tìm hiểu chính mình',
                'en' => 'Understanding Yourself',
                'zh' => '了解自己',
                'ko' => '자신을 이해하기'
            ],
            'description' => [
                'vi' => 'Khám phá bản thân qua các câu hỏi về tính cách và hành vi',
                'en' => 'Discover yourself through questions about personality and behavior',
                'zh' => '通过关于个性和行为的问题发现自己',
                'ko' => '성격과 행동에 대한 질문을 통해 자신을 발견하세요'
            ],
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Create 5 dummy questions with Vietnamese content
        $questions = [
            [
                'content' => [
                    'vi' => 'Bạn thường cảm thấy thoải mái khi giao tiếp với người lạ?',
                    'en' => 'Do you usually feel comfortable talking to strangers?',
                    'zh' => '你通常和陌生人交谈时感到自在吗？',
                    'ko' => '낯선 사람과 대화할 때 보통 편안함을 느끼나요?'
                ],
                'type' => 'single_choice',
                'order' => 1,
                'options' => [
                    ['content' => ['vi' => 'Rất thoải mái', 'en' => 'Very comfortable'], 'score' => 5],
                    ['content' => ['vi' => 'Khá thoải mái', 'en' => 'Quite comfortable'], 'score' => 4],
                    ['content' => ['vi' => 'Bình thường', 'en' => 'Normal'], 'score' => 3],
                    ['content' => ['vi' => 'Hơi ngại', 'en' => 'Slightly uncomfortable'], 'score' => 2],
                    ['content' => ['vi' => 'Rất ngại', 'en' => 'Very uncomfortable'], 'score' => 1],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi đối mặt với khó khăn, bạn thường?',
                    'en' => 'When facing difficulties, you usually?'
                ],
                'type' => 'single_choice',
                'order' => 2,
                'options' => [
                    ['content' => ['vi' => 'Tìm kiếm giải pháp ngay lập tức', 'en' => 'Seek solutions immediately'], 'score' => 5],
                    ['content' => ['vi' => 'Phân tích tình hình trước', 'en' => 'Analyze the situation first'], 'score' => 4],
                    ['content' => ['vi' => 'Hỏi ý kiến người khác', 'en' => 'Ask for others\' opinions'], 'score' => 3],
                    ['content' => ['vi' => 'Tạm thời né tránh', 'en' => 'Temporarily avoid'], 'score' => 2],
                    ['content' => ['vi' => 'Cảm thấy bất lực', 'en' => 'Feel helpless'], 'score' => 1],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn如何看待失败?',
                    'en' => 'How do you view failure?'
                ],
                'type' => 'single_choice',
                'order' => 3,
                'options' => [
                    ['content' => ['vi' => 'Cơ hội để học hỏi', 'en' => 'Opportunity to learn'], 'score' => 5],
                    ['content' => ['vi' => 'Bước tạm thời trên đường thành công', 'en' => 'Temporary step on the path to success'], 'score' => 4],
                    ['content' => ['vi' => 'Điều không may mắn', 'en' => 'Unfortunate thing'], 'score' => 3],
                    ['content' => ['vi' => 'Thất vọng lớn', 'en' => 'Big disappointment'], 'score' => 2],
                    ['content' => ['vi' => 'Điều đáng xấu hổ', 'en' => 'Shameful thing'], 'score' => 1],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn thường dành thời gian rảnh để làm gì?',
                    'en' => 'What do you usually do in your free time?'
                ],
                'type' => 'single_choice',
                'order' => 4,
                'options' => [
                    ['content' => ['vi' => 'Học kỹ năng mới', 'en' => 'Learn new skills'], 'score' => 5],
                    ['content' => ['vi' => 'Đọc sách', 'en' => 'Read books'], 'score' => 4],
                    ['content' => ['vi' => 'Giải trí', 'en' => 'Entertainment'], 'score' => 3],
                    ['content' => ['vi' => 'Nghỉ ngơi', 'en' => 'Rest'], 'score' => 2],
                    ['content' => ['vi' => 'Không có kế hoạch cụ thể', 'en' => 'No specific plan'], 'score' => 1],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi đưa ra quyết định quan trọng, bạn dựa vào?',
                    'en' => 'When making important decisions, you rely on?'
                ],
                'type' => 'single_choice',
                'order' => 5,
                'options' => [
                    ['content' => ['vi' => 'Phân tích logic và dữ liệu', 'en' => 'Logical analysis and data'], 'score' => 5],
                    ['content' => ['vi' => 'Trải nghiệm quá khứ', 'en' => 'Past experience'], 'score' => 4],
                    ['content' => ['vi' => 'Cảm tính', 'en' => 'Intuition'], 'score' => 3],
                    ['content' => ['vi' => 'Ý kiến người khác', 'en' => 'Others\' opinions'], 'score' => 2],
                    ['content' => ['vi' => 'Ngẫu nhiên', 'en' => 'Random chance'], 'score' => 1],
                ]
            ],
        ];

        foreach ($questions as $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $questionData['content'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'score' => $optionData['score'],
                ]);
            }
        }

        $this->command->info('Advanced Assessment "Tìm hiểu chính mình" seeded successfully!');
    }
}
