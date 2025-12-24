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
                    'vi' => 'Khi ai đó tạt đầu xe bạn trên đường, phản ứng đầu tiên của bạn là gì?',
                    'en' => 'When someone cuts you off in traffic, what is your first reaction?'
                ],
                'pillar_group' => 'heart',
                'order' => 1,
                'answers' => [
                    ['content' => [
                        'vi' => 'Chửi thề, bực bội muốn đuổi theo hoặc bấm còi inh ỏi.',
                        'en' => 'Swear, feel angry and want to chase them or honk loudly.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Hơi giật mình và khó chịu, nhưng rồi bỏ qua.',
                        'en' => 'Startled and uncomfortable, but then let it go.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Thở phào vì mình không sao, cầu mong họ đi cẩn thận hơn.',
                        'en' => 'Relieved that I\'m okay, hope they drive more carefully.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có hay nói dối (kể cả nói dối nhỏ "white lies") để tránh phiền phức không?',
                    'en' => 'Do you often lie (including "white lies") to avoid trouble?'
                ],
                'pillar_group' => 'heart',
                'order' => 2,
                'answers' => [
                    ['content' => [
                        'vi' => 'Thường xuyên, đó là cách khôn ngoan để sống.',
                        'en' => 'Often, that\'s the smart way to live.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Thỉnh thoảng, khi tình thế bắt buộc.',
                        'en' => 'Sometimes, when circumstances force it.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Hiếm khi, tôi coi trọng sự trung thực dù sự thật mất lòng.',
                        'en' => 'Rarely, I value honesty even if the truth hurts.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi thấy một đồng nghiệp/người quen đạt được thành công lớn hơn mình:',
                    'en' => 'When you see a colleague/acquaintain achieve greater success than you:'
                ],
                'pillar_group' => 'heart',
                'order' => 3,
                'answers' => [
                    ['content' => [
                        'vi' => 'Tôi thấy ghen tị và tìm cách bới móc khuyết điểm của họ.',
                        'en' => 'I feel jealous and try to find their flaws.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Tôi thấy hơi chạnh lòng một chút nhưng vẫn chúc mừng họ.',
                        'en' => 'I feel a bit upset but still congratulate them.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Tôi thực sự vui mừng cho họ và lấy đó làm động lực.',
                        'en' => 'I\'m genuinely happy for them and use it as motivation.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mối quan hệ của bạn với bố mẹ/người thân hiện tại thế nào?',
                    'en' => 'How is your relationship with your parents/relatives now?'
                ],
                'pillar_group' => 'heart',
                'order' => 4,
                'answers' => [
                    ['content' => [
                        'vi' => 'Rất tệ, thường xuyên cãi vã hoặc chiến tranh lạnh.',
                        'en' => 'Very bad, frequent arguments or cold wars.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Bình thường, có mâu thuẫn nhưng vẫn nói chuyện được.',
                        'en' => 'Normal, have conflicts but can still talk.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Gắn kết, thấu hiểu và thường xuyên chia sẻ.',
                        'en' => 'Connected, understanding and frequently sharing.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có thường xuyên phán xét ngoại hình hay đời tư của người khác không?',
                    'en' => 'Do you often judge others\' appearance or private life?'
                ],
                'pillar_group' => 'heart',
                'order' => 5,
                'answers' => [
                    ['content' => [
                        'vi' => 'Có, đó là niềm vui khi buôn chuyện với bạn bè.',
                        'en' => 'Yes, it\'s fun to gossip with friends.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Đôi khi buột miệng nhưng sau đó thấy hối hận.',
                        'en' => 'Sometimes slip up but then regret it.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Không, tôi hiểu mỗi người đều có hoàn cảnh riêng.',
                        'en' => 'No, I understand everyone has their own circumstances.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn đối xử với người phục vụ (bảo vệ, shipper, nhân viên bàn) thế nào khi họ làm sai?',
                    'en' => 'How do you treat service staff (security, delivery, waiters) when they make mistakes?'
                ],
                'pillar_group' => 'heart',
                'order' => 6,
                'answers' => [
                    ['content' => [
                        'vi' => 'Quát mắng để họ nhớ đời.',
                        'en' => 'Yell at them so they remember.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Nhắc nhở nghiêm khắc nhưng không xúc phạm.',
                        'en' => 'Remind sternly but without insults.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Nhẹ nhàng góp ý và thông cảm cho sự vất vả của họ.',
                        'en' => 'Gently suggest and sympathize with their hard work.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mức độ sẵn sàng giúp đỡ người lạ của bạn?',
                    'en' => 'How willing are you to help strangers?'
                ],
                'pillar_group' => 'heart',
                'order' => 7,
                'answers' => [
                    ['content' => [
                        'vi' => 'Không bao giờ, việc ai nấy lo.',
                        'en' => 'Never, everyone takes care of themselves.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Tùy hứng và tùy xem có thuận tiện không.',
                        'en' => 'Depends on mood and convenience.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Luôn sẵn lòng trong khả năng của mình.',
                        'en' => 'Always willing within my ability.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có tha thứ được cho người từng làm tổn thương bạn trong quá khứ không?',
                    'en' => 'Can you forgive someone who hurt you in the past?'
                ],
                'pillar_group' => 'heart',
                'order' => 8,
                'answers' => [
                    ['content' => [
                        'vi' => 'Không, tôi sẽ ghim hận và nhớ mãi.',
                        'en' => 'No, I\'ll hold a grudge and remember forever.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Tôi cố quên nhưng khi nhắc lại vẫn thấy đau/giận.',
                        'en' => 'I try to forget but still feel hurt/angry when reminded.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Tôi đã buông bỏ và cầu chúc họ bình an.',
                        'en' => 'I have let go and wish them peace.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có thường xuyên sát sinh (giết côn trùng, thú vật...) không cần thiết?',
                    'en' => 'Do you often kill living beings (insects, animals...) unnecessarily?'
                ],
                'pillar_group' => 'heart',
                'order' => 9,
                'answers' => [
                    ['content' => [
                        'vi' => 'Thấy là đập, không quan trọng.',
                        'en' => 'Kill when I see them, doesn\'t matter.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Chỉ khi chúng gây hại (muỗi, gián).',
                        'en' => 'Only when they cause harm (mosquitoes, cockroaches).'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Tôi cố gắng tránh làm hại mọi sinh vật sống hết mức có thể.',
                        'en' => 'I try to avoid harming all living beings as much as possible.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Tần suất bạn cảm thấy cô đơn và mất kết nối với thế giới?',
                    'en' => 'How often do you feel lonely and disconnected from the world?'
                ],
                'pillar_group' => 'heart',
                'order' => 10,
                'answers' => [
                    ['content' => [
                        'vi' => 'Rất thường xuyên.',
                        'en' => 'Very often.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Thỉnh thoảng.',
                        'en' => 'Sometimes.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Hiếm khi, tôi thấy mình chan hòa với mọi người.',
                        'en' => 'Rarely, I feel harmonious with everyone.'
                    ], 'score' => 5],
                ]
            ],

            // GROUP 2: GRIT (Questions 11-20)
            [
                'content' => [
                    'vi' => 'Bạn có thể ngồi yên không làm gì (không điện thoại, không nhạc) trong bao lâu?',
                    'en' => 'How long can you sit still doing nothing (no phone, no music)?'
                ],
                'pillar_group' => 'grit',
                'order' => 11,
                'answers' => [
                    ['content' => [
                        'vi' => 'Dưới 2 phút là thấy bứt rứt không chịu nổi.',
                        'en' => 'Under 2 minutes, I feel restless and can\'t stand it.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Khoảng 5-10 phút.',
                        'en' => 'About 5-10 minutes.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Trên 20 phút mà vẫn thấy thoải mái.',
                        'en' => 'Over 20 minutes and still feel comfortable.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi gặp một thất bại lớn (trượt phỏng vấn, mất tiền...), bạn thường mất bao lâu để cân bằng lại?',
                    'en' => 'When facing a major failure (failed interview, lost money...), how long does it take to recover?'
                ],
                'pillar_group' => 'grit',
                'order' => 12,
                'answers' => [
                    ['content' => [
                        'vi' => 'Nhiều tuần hoặc nhiều tháng chìm trong đau khổ.',
                        'en' => 'Many weeks or months drowning in sorrow.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Vài ngày buồn bã.',
                        'en' => 'A few days of sadness.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Chỉ vài giờ hoặc 1 ngày, sau đó tôi tìm giải pháp tiếp theo.',
                        'en' => 'Just a few hours or 1 day, then I find the next solution.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có bị nghiện mạng xã hội (Facebook/TikTok) không?',
                    'en' => 'Are you addicted to social media (Facebook/TikTok)?'
                ],
                'pillar_group' => 'grit',
                'order' => 13,
                'answers' => [
                    ['content' => [
                        'vi' => 'Có, tôi lướt trong vô thức hàng giờ mỗi ngày.',
                        'en' => 'Yes, I scroll mindlessly for hours every day.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Có dùng nhiều nhưng vẫn kiểm soát được khi cần làm việc.',
                        'en' => 'Use it a lot but can control when I need to work.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Không, tôi dùng rất ít và có mục đích rõ ràng.',
                        'en' => 'No, I use it very little and with clear purpose.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Chất lượng giấc ngủ của bạn thế nào?',
                    'en' => 'How is your sleep quality?'
                ],
                'pillar_group' => 'grit',
                'order' => 14,
                'answers' => [
                    ['content' => [
                        'vi' => 'Khó ngủ, trằn trọc, hay gặp ác mộng.',
                        'en' => 'Hard to sleep, toss and turn, often have nightmares.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Thỉnh thoảng mất ngủ khi có chuyện lo nghĩ.',
                        'en' => 'Sometimes lose sleep when worried.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Đặt lưng là ngủ, giấc ngủ sâu.',
                        'en' => 'Fall asleep immediately, deep sleep.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi tức giận, bạn thường phản ứng ra sao?',
                    'en' => 'When angry, how do you usually react?'
                ],
                'pillar_group' => 'grit',
                'order' => 15,
                'answers' => [
                    ['content' => [
                        'vi' => 'Bùng nổ ngay lập tức (đập đồ, quát tháo).',
                        'en' => 'Explode immediately (break things, yell).'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Cố kìm nén vào trong nhưng mặt hầm hầm.',
                        'en' => 'Try to suppress inside but look grumpy.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Quan sát hơi thở, chờ cơn giận đi qua rồi mới nói chuyện.',
                        'en' => 'Observe my breath, wait for anger to pass before speaking.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mức độ tập trung của bạn trong công việc?',
                    'en' => 'How is your concentration at work?'
                ],
                'pillar_group' => 'grit',
                'order' => 16,
                'answers' => [
                    ['content' => [
                        'vi' => 'Rất kém, cứ 5 phút lại phải check điện thoại một lần.',
                        'en' => 'Very poor, check phone every 5 minutes.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Tạm ổn, nhưng dễ bị xao nhãng bởi tiếng ồn.',
                        'en' => 'Okay, but easily distracted by noise.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Rất tốt, có thể làm việc sâu (Deep Work) hàng giờ liền.',
                        'en' => 'Very good, can do deep work for hours.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có hay trì hoãn (procrastinate) những việc quan trọng không?',
                    'en' => 'Do you often procrastinate on important tasks?'
                ],
                'pillar_group' => 'grit',
                'order' => 17,
                'answers' => [
                    ['content' => [
                        'vi' => 'Luôn luôn, nước đến chân mới nhảy.',
                        'en' => 'Always, only act when desperate.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Thỉnh thoảng.',
                        'en' => 'Sometimes.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Hiếm khi, tôi có kỷ luật làm việc theo kế hoạch.',
                        'en' => 'Rarely, I have discipline to work according to plan.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có duy trì được thói quen tập thể dục không?',
                    'en' => 'Can you maintain exercise habits?'
                ],
                'pillar_group' => 'grit',
                'order' => 18,
                'answers' => [
                    ['content' => [
                        'vi' => 'Không, tôi lười vận động.',
                        'en' => 'No, I\'m lazy about exercise.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Bữa đực bữa cái, hào hứng lúc đầu rồi bỏ.',
                        'en' => 'On and off, excited at first then quit.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Đều đặn hàng tuần.',
                        'en' => 'Consistently every week.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Cảm xúc chủ đạo trong ngày của bạn là gì?',
                    'en' => 'What is your dominant emotion during the day?'
                ],
                'pillar_group' => 'grit',
                'order' => 19,
                'answers' => [
                    ['content' => [
                        'vi' => 'Lo âu, bồn chồn hoặc chán nản.',
                        'en' => 'Anxiety, restlessness or depression.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Bình bình, không vui không buồn.',
                        'en' => 'Neutral, neither happy nor sad.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'An yên, nhẹ nhàng.',
                        'en' => 'Peaceful, gentle.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có dễ bị tác động bởi lời khen chê của người khác không?',
                    'en' => 'Are you easily affected by others\' praise or criticism?'
                ],
                'pillar_group' => 'grit',
                'order' => 20,
                'answers' => [
                    ['content' => [
                        'vi' => 'Rất dễ, ai chê là buồn cả ngày, ai khen là lên mây.',
                        'en' => 'Very easily, sad all day if criticized, on cloud nine if praised.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Có để tâm nhưng không bị ảnh hưởng quá lâu.',
                        'en' => 'Care but not affected for too long.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Không, tôi hiểu giá trị của mình không nằm ở miệng lưỡi thế gian.',
                        'en' => 'No, I understand my value doesn\'t lie in others\' words.'
                    ], 'score' => 5],
                ]
            ],

            // GROUP 3: WISDOM (Questions 21-30)
            [
                'content' => [
                    'vi' => 'Khi gặp chuyện xui xẻo, bạn thường nghĩ gì?',
                    'en' => 'When something unlucky happens, what do you usually think?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 21,
                'answers' => [
                    ['content' => [
                        'vi' => 'Tại sao lại là tôi? Ông trời thật bất công! (Đổ lỗi).',
                        'en' => 'Why me? Heaven is so unfair! (Blame others).'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Chắc do mình xui hoặc do người khác gây ra.',
                        'en' => 'Probably my bad luck or someone else caused it.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Chắc chắn có nguyên nhân gì đó từ trước (Nhân quả), mình cần học bài học này.',
                        'en' => 'There must be some previous cause (karma), I need to learn this lesson.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn hiểu thế nào về "Hạnh phúc"?',
                    'en' => 'How do you understand "Happiness"?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 22,
                'answers' => [
                    ['content' => [
                        'vi' => 'Là có nhiều tiền, nhà đẹp, xe sang và người yêu đẹp.',
                        'en' => 'Having lots of money, nice house, luxury car and beautiful partner.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Là công việc ổn định và gia đình êm ấm.',
                        'en' => 'Stable job and warm family.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Là sự bình an trong tâm hồn, không phụ thuộc vào vật chất bên ngoài.',
                        'en' => 'Peace in the soul, independent of external material things.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn có tin vào mê tín dị đoan (cúng sao giải hạn, xem bói định mệnh) không?',
                    'en' => 'Do you believe in superstitions (offering to stars, fortune telling)?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 23,
                'answers' => [
                    ['content' => [
                        'vi' => 'Rất tin, tôi thường xuyên đi xem bói để quyết định việc lớn.',
                        'en' => 'Very much, I often go to fortune tellers to make big decisions.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Nửa tin nửa ngờ, có thờ có thiêng.',
                        'en' => 'Half believe, half doubt, respect supernatural.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Không, tôi tin vào quy luật Nhân - Quả và nỗ lực của bản thân.',
                        'en' => 'No, I believe in karma law and personal effort.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Khi tranh luận, mục tiêu của bạn là gì?',
                    'en' => 'When debating, what is your goal?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 24,
                'answers' => [
                    ['content' => [
                        'vi' => 'Phải thắng cho bằng được, chứng minh mình đúng.',
                        'en' => 'Must win at all costs, prove I\'m right.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Để bảo vệ quan điểm của mình.',
                        'en' => 'To defend my viewpoint.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Để tìm ra sự thật và học hỏi góc nhìn mới.',
                        'en' => 'To find the truth and learn new perspectives.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn đối diện với cái chết hoặc sự chia ly như thế nào?',
                    'en' => 'How do you face death or separation?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 25,
                'answers' => [
                    ['content' => [
                        'vi' => 'Sợ hãi tột độ, không dám nghĩ tới.',
                        'en' => 'Extremely scared, don\'t dare to think about it.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Lo lắng nhưng biết đó là điều khó tránh.',
                        'en' => 'Worried but know it\'s hard to avoid.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Chấp nhận đó là quy luật Vô thường của cuộc sống, nên trân trọng hiện tại hơn.',
                        'en' => 'Accept it as the law of impermanence, should cherish the present more.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn đánh giá thế nào về sự trung thực của bản thân với chính mình?',
                    'en' => 'How do you rate your honesty with yourself?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 26,
                'answers' => [
                    ['content' => [
                        'vi' => 'Tôi thường tự lừa dối bản thân rằng "mọi thứ vẫn ổn".',
                        'en' => 'I often deceive myself that "everything is fine".'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Đôi khi tôi không dám nhìn thẳng vào yếu điểm của mình.',
                        'en' => 'Sometimes I don\'t dare to face my weaknesses directly.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Tôi thường xuyên soi lỗi của mình để sửa đổi.',
                        'en' => 'I often reflect on my faults to improve.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Theo bạn, nguyên nhân gốc rễ của khổ đau là gì?',
                    'en' => 'In your opinion, what is the root cause of suffering?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 27,
                'answers' => [
                    ['content' => [
                        'vi' => 'Do nghèo, do người khác xấu tính, do hoàn cảnh.',
                        'en' => 'Due to poverty, others\' bad character, circumstances.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Do mình chưa đủ giỏi hoặc chưa may mắn.',
                        'en' => 'Because I\'m not good enough or not lucky enough.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Do lòng tham cầu và sự dính mắc (mong cầu mọi thứ theo ý mình).',
                        'en' => 'Due to craving and attachment (wanting everything to go my way).'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn giải quyết vấn đề bằng cách nào?',
                    'en' => 'How do you solve problems?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 28,
                'answers' => [
                    ['content' => [
                        'vi' => 'Làm theo cảm tính hoặc đám đông mách bảo.',
                        'en' => 'Follow emotions or crowd suggestions.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Hỏi ý kiến người thân.',
                        'en' => 'Ask family members\' opinions.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Quan sát, phân tích nguyên nhân - kết quả rồi mới quyết định.',
                        'en' => 'Observe, analyze cause-effect then decide.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Bạn dành bao nhiêu thời gian để đọc sách hoặc học điều mới mỗi tuần?',
                    'en' => 'How much time do you spend reading or learning new things each week?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 29,
                'answers' => [
                    ['content' => [
                        'vi' => 'Hầu như không đọc, chỉ xem video giải trí.',
                        'en' => 'Almost never read, only watch entertainment videos.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Thỉnh thoảng đọc khi rảnh.',
                        'en' => 'Sometimes read when free.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Đều đặn, tôi coi trọng việc vun bồi trí tuệ.',
                        'en' => 'Consistently, I value cultivating wisdom.'
                    ], 'score' => 5],
                ]
            ],
            [
                'content' => [
                    'vi' => 'Mục đích sống hiện tại của bạn là gì?',
                    'en' => 'What is your current life purpose?'
                ],
                'pillar_group' => 'wisdom',
                'order' => 30,
                'answers' => [
                    ['content' => [
                        'vi' => 'Kiếm thật nhiều tiền và hưởng thụ.',
                        'en' => 'Make lots of money and enjoy life.'
                    ], 'score' => 1],
                    ['content' => [
                        'vi' => 'Lo cho gia đình đầy đủ.',
                        'en' => 'Provide for family adequately.'
                    ], 'score' => 3],
                    ['content' => [
                        'vi' => 'Sống có ích, cống hiến và tìm kiếm sự giải thoát khổ đau.',
                        'en' => 'Live usefully, contribute and seek liberation from suffering.'
                    ], 'score' => 5],
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
