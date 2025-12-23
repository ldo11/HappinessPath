<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('assessment_answers')->delete();
        DB::table('assessment_questions')->delete();

        // Assessment questions data
        $questions = [
            // GROUP 1: HEART (Questions 1-10)
            [
                'content' => [
                    'vi' => 'Khi ai đó tạt đầu xe bạn trên đường, phản ứng đầu tiên của bạn là gì?'
                ],
                'pillar_group' => 'heart',
                'order' => 1,
                'answers' => [
                    ['content' => ['vi' => 'Chửi thề, bực bội muốn đuổi theo hoặc bấm còi inh ỏi.'], 'score' => 1],
                    ['content' => ['vi' => 'Hơi giật mình và khó chịu, nhưng rồi bỏ qua.'], 'score' => 3],
                    ['content' => ['vi' => 'Thở phào vì mình không sao, cầu mong họ đi cẩn thận hơn.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có hay nói dối (kể cả nói dối nhỏ "white lies") để tránh phiền phức không?'
                ],
                'pillar_group' => 'heart',
                'order' => 2,
                'answers' => [
                    ['content' => ['vi' => 'Thường xuyên, đó là cách khôn ngoan để sống.'], 'score' => 1],
                    ['content' => ['vi' => 'Thỉnh thoảng, khi tình thế bắt buộc.'], 'score' => 3],
                    ['content' => ['vi' => 'Hiếm khi, tôi coi trọng sự trung thực dù sự thật mất lòng.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi thấy một đồng nghiệp/người quen đạt được thành công lớn hơn mình:'
                ],
                'pillar_group' => 'heart',
                'order' => 3,
                'answers' => [
                    ['content' => ['vi' => 'Tôi thấy ghen tị và tìm cách bới móc khuyết điểm của họ.'], 'score' => 1],
                    ['content' => ['vi' => 'Tôi thấy hơi chạnh lòng một chút nhưng vẫn chúc mừng họ.'], 'score' => 3],
                    ['content' => ['vi' => 'Tôi thực sự vui mừng cho họ và lấy đó làm động lực.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mối quan hệ của bạn với bố mẹ/người thân hiện tại thế nào?'
                ],
                'pillar_group' => 'heart',
                'order' => 4,
                'answers' => [
                    ['content' => ['vi' => 'Rất tệ, thường xuyên cãi vã hoặc chiến tranh lạnh.'], 'score' => 1],
                    ['content' => ['vi' => 'Bình thường, có mâu thuẫn nhưng vẫn nói chuyện được.'], 'score' => 3],
                    ['content' => ['vi' => 'Gắn kết, thấu hiểu và thường xuyên chia sẻ.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có thường xuyên phán xét ngoại hình hay đời tư của người khác không?'
                ],
                'pillar_group' => 'heart',
                'order' => 5,
                'answers' => [
                    ['content' => ['vi' => 'Có, đó là niềm vui khi buôn chuyện với bạn bè.'], 'score' => 1],
                    ['content' => ['vi' => 'Đôi khi buột miệng nhưng sau đó thấy hối hận.'], 'score' => 3],
                    ['content' => ['vi' => 'Không, tôi hiểu mỗi người đều có hoàn cảnh riêng.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn đối xử với người phục vụ (bảo vệ, shipper, nhân viên bàn) thế nào khi họ làm sai?'
                ],
                'pillar_group' => 'heart',
                'order' => 6,
                'answers' => [
                    ['content' => ['vi' => 'Quát mắng để họ nhớ đời.'], 'score' => 1],
                    ['content' => ['vi' => 'Nhắc nhở nghiêm khắc nhưng không xúc phạm.'], 'score' => 3],
                    ['content' => ['vi' => 'Nhẹ nhàng góp ý và thông cảm cho sự vất vả của họ.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mức độ sẵn sàng giúp đỡ người lạ của bạn?'
                ],
                'pillar_group' => 'heart',
                'order' => 7,
                'answers' => [
                    ['content' => ['vi' => 'Không bao giờ, việc ai nấy lo.'], 'score' => 1],
                    ['content' => ['vi' => 'Tùy hứng và tùy xem có thuận tiện không.'], 'score' => 3],
                    ['content' => ['vi' => 'Luôn sẵn lòng trong khả năng của mình.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có tha thứ được cho người từng làm tổn thương bạn trong quá khứ không?'
                ],
                'pillar_group' => 'heart',
                'order' => 8,
                'answers' => [
                    ['content' => ['vi' => 'Không, tôi sẽ ghim hận và nhớ mãi.'], 'score' => 1],
                    ['content' => ['vi' => 'Tôi cố quên nhưng khi nhắc lại vẫn thấy đau/giận.'], 'score' => 3],
                    ['content' => ['vi' => 'Tôi đã buông bỏ và cầu chúc họ bình an.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có thường xuyên sát sinh (giết côn trùng, thú vật...) không cần thiết?'
                ],
                'pillar_group' => 'heart',
                'order' => 9,
                'answers' => [
                    ['content' => ['vi' => 'Thấy là đập, không quan trọng.'], 'score' => 1],
                    ['content' => ['vi' => 'Chỉ khi chúng gây hại (muỗi, gián).'], 'score' => 3],
                    ['content' => ['vi' => 'Tôi cố gắng tránh làm hại mọi sinh vật sống hết mức có thể.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Tần suất bạn cảm thấy cô đơn và mất kết nối với thế giới?'
                ],
                'pillar_group' => 'heart',
                'order' => 10,
                'answers' => [
                    ['content' => ['vi' => 'Rất thường xuyên.'], 'score' => 1],
                    ['content' => ['vi' => 'Thỉnh thoảng.'], 'score' => 3],
                    ['content' => ['vi' => 'Hiếm khi, tôi thấy mình chan hòa với mọi người.'], 'score' => 5],
                ]
            ],

            // GROUP 2: GRIT (Questions 11-20)
            [
                'content' => [
                    'vi' => 'Bạn có thể ngồi yên không làm gì (không điện thoại, không nhạc) trong bao lâu?'
                ],
                'pillar_group' => 'grit',
                'order' => 11,
                'answers' => [
                    ['content' => ['vi' => 'Dưới 2 phút là thấy bứt rứt không chịu nổi.'], 'score' => 1],
                    ['content' => ['vi' => 'Khoảng 5-10 phút.'], 'score' => 3],
                    ['content' => ['vi' => 'Trên 20 phút mà vẫn thấy thoải mái.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi gặp một thất bại lớn (trượt phỏng vấn, mất tiền...), bạn thường mất bao lâu để cân bằng lại?'
                ],
                'pillar_group' => 'grit',
                'order' => 12,
                'answers' => [
                    ['content' => ['vi' => 'Nhiều tuần hoặc nhiều tháng chìm trong đau khổ.'], 'score' => 1],
                    ['content' => ['vi' => 'Vài ngày buồn bã.'], 'score' => 3],
                    ['content' => ['vi' => 'Chỉ vài giờ hoặc 1 ngày, sau đó tôi tìm giải pháp tiếp theo.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có bị nghiện mạng xã hội (Facebook/TikTok) không?'
                ],
                'pillar_group' => 'grit',
                'order' => 13,
                'answers' => [
                    ['content' => ['vi' => 'Có, tôi lướt trong vô thức hàng giờ mỗi ngày.'], 'score' => 1],
                    ['content' => ['vi' => 'Có dùng nhiều nhưng vẫn kiểm soát được khi cần làm việc.'], 'score' => 3],
                    ['content' => ['vi' => 'Không, tôi dùng rất ít và có mục đích rõ ràng.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Chất lượng giấc ngủ của bạn thế nào?'
                ],
                'pillar_group' => 'grit',
                'order' => 14,
                'answers' => [
                    ['content' => ['vi' => 'Khó ngủ, trằn trọc, hay gặp ác mộng.'], 'score' => 1],
                    ['content' => ['vi' => 'Thỉnh thoảng mất ngủ khi có chuyện lo nghĩ.'], 'score' => 3],
                    ['content' => ['vi' => 'Đặt lưng là ngủ, giấc ngủ sâu.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi tức giận, bạn thường phản ứng ra sao?'
                ],
                'pillar_group' => 'grit',
                'order' => 15,
                'answers' => [
                    ['content' => ['vi' => 'Bùng nổ ngay lập tức (đập đồ, quát tháo).'], 'score' => 1],
                    ['content' => ['vi' => 'Cố kìm nén vào trong nhưng mặt hầm hầm.'], 'score' => 3],
                    ['content' => ['vi' => 'Quan sát hơi thở, chờ cơn giận đi qua rồi mới nói chuyện.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mức độ tập trung của bạn trong công việc?'
                ],
                'pillar_group' => 'grit',
                'order' => 16,
                'answers' => [
                    ['content' => ['vi' => 'Rất kém, cứ 5 phút lại phải check điện thoại một lần.'], 'score' => 1],
                    ['content' => ['vi' => 'Tạm ổn, nhưng dễ bị xao nhãng bởi tiếng ồn.'], 'score' => 3],
                    ['content' => ['vi' => 'Rất tốt, có thể làm việc sâu (Deep Work) hàng giờ liền.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có hay trì hoãn (procrastinate) những việc quan trọng không?'
                ],
                'pillar_group' => 'grit',
                'order' => 17,
                'answers' => [
                    ['content' => ['vi' => 'Luôn luôn, nước đến chân mới nhảy.'], 'score' => 1],
                    ['content' => ['vi' => 'Thỉnh thoảng.'], 'score' => 3],
                    ['content' => ['vi' => 'Hiếm khi, tôi có kỷ luật làm việc theo kế hoạch.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có duy trì được thói quen tập thể dục không?'
                ],
                'pillar_group' => 'grit',
                'order' => 18,
                'answers' => [
                    ['content' => ['vi' => 'Không, tôi lười vận động.'], 'score' => 1],
                    ['content' => ['vi' => 'Bữa đực bữa cái, hào hứng lúc đầu rồi bỏ.'], 'score' => 3],
                    ['content' => ['vi' => 'Đều đặn hàng tuần.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Cảm xúc chủ đạo trong ngày của bạn là gì?'
                ],
                'pillar_group' => 'grit',
                'order' => 19,
                'answers' => [
                    ['content' => ['vi' => 'Lo âu, bồn chồn hoặc chán nản.'], 'score' => 1],
                    ['content' => ['vi' => 'Bình bình, không vui không buồn.'], 'score' => 3],
                    ['content' => ['vi' => 'An yên, nhẹ nhàng.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có dễ bị tác động bởi lời khen chê của người khác không?'
                ],
                'pillar_group' => 'grit',
                'order' => 20,
                'answers' => [
                    ['content' => ['vi' => 'Rất dễ, ai chê là buồn cả ngày, ai khen là lên mây.'], 'score' => 1],
                    ['content' => ['vi' => 'Có để tâm nhưng không bị ảnh hưởng quá lâu.'], 'score' => 3],
                    ['content' => ['vi' => 'Không, tôi hiểu giá trị của mình không nằm ở miệng lưỡi thế gian.'], 'score' => 5],
                ]
            ],

            // GROUP 3: WISDOM (Questions 21-30)
            [
                'content' => [
                    'vi' => 'Khi gặp chuyện xui xẻo, bạn thường nghĩ gì?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 21,
                'answers' => [
                    ['content' => ['vi' => 'Tại sao lại là tôi? Ông trời thật bất công! (Đổ lỗi).'], 'score' => 1],
                    ['content' => ['vi' => 'Chắc do mình xui hoặc do người khác gây ra.'], 'score' => 3],
                    ['content' => ['vi' => 'Chắc chắn có nguyên nhân gì đó từ trước (Nhân quả), mình cần học bài học này.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn hiểu thế nào về "Hạnh phúc"?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 22,
                'answers' => [
                    ['content' => ['vi' => 'Là có nhiều tiền, nhà đẹp, xe sang và người yêu đẹp.'], 'score' => 1],
                    ['content' => ['vi' => 'Là công việc ổn định và gia đình êm ấm.'], 'score' => 3],
                    ['content' => ['vi' => 'Là sự bình an trong tâm hồn, không phụ thuộc vào vật chất bên ngoài.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có tin vào mê tín dị đoan (cúng sao giải hạn, xem bói định mệnh) không?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 23,
                'answers' => [
                    ['content' => ['vi' => 'Rất tin, tôi thường xuyên đi xem bói để quyết định việc lớn.'], 'score' => 1],
                    ['content' => ['vi' => 'Nửa tin nửa ngờ, có thờ có thiêng.'], 'score' => 3],
                    ['content' => ['vi' => 'Không, tôi tin vào quy luật Nhân - Quả và nỗ lực của bản thân.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi tranh luận, mục tiêu của bạn là gì?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 24,
                'answers' => [
                    ['content' => ['vi' => 'Phải thắng cho bằng được, chứng minh mình đúng.'], 'score' => 1],
                    ['content' => ['vi' => 'Để bảo vệ quan điểm của mình.'], 'score' => 3],
                    ['content' => ['vi' => 'Để tìm ra sự thật và học hỏi góc nhìn mới.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn đối diện với cái chết hoặc sự chia ly như thế nào?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 25,
                'answers' => [
                    ['content' => ['vi' => 'Sợ hãi tột độ, không dám nghĩ tới.'], 'score' => 1],
                    ['content' => ['vi' => 'Lo lắng nhưng biết đó là điều khó tránh.'], 'score' => 3],
                    ['content' => ['vi' => 'Chấp nhận đó là quy luật Vô thường của cuộc sống, nên trân trọng hiện tại hơn.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn đánh giá thế nào về sự trung thực của bản thân với chính mình?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 26,
                'answers' => [
                    ['content' => ['vi' => 'Tôi thường tự lừa dối bản thân rằng "mọi thứ vẫn ổn".'], 'score' => 1],
                    ['content' => ['vi' => 'Đôi khi tôi không dám nhìn thẳng vào yếu điểm của mình.'], 'score' => 3],
                    ['content' => ['vi' => 'Tôi thường xuyên soi lỗi của mình để sửa đổi.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Theo bạn, nguyên nhân gốc rễ của khổ đau là gì?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 27,
                'answers' => [
                    ['content' => ['vi' => 'Do nghèo, do người khác xấu tính, do hoàn cảnh.'], 'score' => 1],
                    ['content' => ['vi' => 'Do mình chưa đủ giỏi hoặc chưa may mắn.'], 'score' => 3],
                    ['content' => ['vi' => 'Do lòng tham cầu và sự dính mắc (mong cầu mọi thứ theo ý mình).'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn giải quyết vấn đề bằng cách nào?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 28,
                'answers' => [
                    ['content' => ['vi' => 'Làm theo cảm tính hoặc đám đông mách bảo.'], 'score' => 1],
                    ['content' => ['vi' => 'Hỏi ý kiến người thân.'], 'score' => 3],
                    ['content' => ['vi' => 'Quan sát, phân tích nguyên nhân - kết quả rồi mới quyết định.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn dành bao nhiêu thời gian để đọc sách hoặc học điều mới mỗi tuần?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 29,
                'answers' => [
                    ['content' => ['vi' => 'Hầu như không đọc, chỉ xem video giải trí.'], 'score' => 1],
                    ['content' => ['vi' => 'Thỉnh thoảng đọc khi rảnh.'], 'score' => 3],
                    ['content' => ['vi' => 'Đều đặn, tôi coi trọng việc vun bồi trí tuệ.'], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mục đích sống hiện tại của bạn là gì?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 30,
                'answers' => [
                    ['content' => ['vi' => 'Kiếm thật nhiều tiền và hưởng thụ.'], 'score' => 1],
                    ['content' => ['vi' => 'Lo cho gia đình đầy đủ.'], 'score' => 3],
                    ['content' => ['vi' => 'Sống có ích, cống hiến và tìm kiếm sự giải thoát khổ đau.'], 'score' => 5],
                ]
            ],
        ];

        // Insert questions and their answers
        foreach ($questions as $questionData) {
            // Insert question
            $questionId = DB::table('assessment_questions')->insertGetId([
                'content' => json_encode($questionData['content']),
                'pillar_group' => $questionData['pillar_group'],
                'order' => $questionData['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert answers for this question
            foreach ($questionData['answers'] as $index => $answerData) {
                DB::table('assessment_answers')->insert([
                    'question_id' => $questionId,
                    'content' => json_encode($answerData['content']),
                    'score' => $answerData['score'],
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Assessment questions and answers seeded successfully!');
    }
}
