<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cleanup
        Schema::disableForeignKeyConstraints();
        DB::table('assessment_options')->truncate();
        DB::table('assessment_questions')->truncate();
        DB::table('assessments')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Create Admin User if needed
        $user = User::firstOrCreate(
            ['email' => 'admin@happiness.test'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Assessment
        $assessment = Assessment::create([
            'title' => [
                'en' => 'Deep Self-Discovery',
                'vi' => 'Khám Phá Bản Thân Sâu Sắc',
                'de' => 'Tiefe Selbstfindung',
                'kr' => '깊은 자아 발견',
            ],
            'description' => [
                'en' => 'A comprehensive assessment to understand your Body, Mind, and Wisdom.',
                'vi' => 'Bài kiểm tra toàn diện để thấu hiểu Thân, Tâm và Trí của bạn.',
                'de' => 'Eine umfassende Bewertung zum Verständnis von Körper, Geist und Weisheit.',
                'kr' => '몸과 마음, 지혜를 이해하기 위한 종합적인 평가입니다.',
            ],
            'status' => 'active',
            'created_by' => $user->id,
            'score_ranges' => [
                ['min' => 30, 'max' => 60, 'label' => 'Need Improvement / Cần Cải Thiện'],
                ['min' => 61, 'max' => 90, 'label' => 'Good Balance / Cân Bằng Tốt'],
                ['min' => 91, 'max' => 120, 'label' => 'Excellent Harmony / Hài Hòa Tuyệt Vời'],
            ],
        ]);

        $questions = [
            // --- BODY (Questions 1-5) ---
            [
                'order' => 1,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How is your sleep quality?',
                    'vi' => 'Chất lượng giấc ngủ của bạn thế nào?',
                    'de' => 'Wie ist Ihre Schlafqualität?',
                    'kr' => '수면의 질은 어떠신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Terrible, I toss and turn all night.', 'vi' => 'Rất tệ, trằn trọc cả đêm.', 'de' => 'Schrecklich, ich wälze mich die ganze Nacht.', 'kr' => '끔찍해요, 밤새 뒤척입니다.']],
                    ['score' => 2, 'content' => ['en' => 'Not good, often wake up tired.', 'vi' => 'Không tốt, thường xuyên dậy mệt mỏi.', 'de' => 'Nicht gut, wache oft müde auf.', 'kr' => '좋지 않아요, 자주 피곤하게 깨어납니다.']],
                    ['score' => 3, 'content' => ['en' => 'Average, sleep okay mostly.', 'vi' => 'Bình thường, ngủ tạm ổn.', 'de' => 'Durchschnittlich, schlafe meistens okay.', 'kr' => '보통이에요, 대체로 잘 잡니다.']],
                    ['score' => 4, 'content' => ['en' => 'Excellent, deep and restorative.', 'vi' => 'Tuyệt vời, ngủ sâu và hồi phục tốt.', 'de' => 'Ausgezeichnet, tief und erholsam.', 'kr' => '훌륭해요, 깊고 회복이 잘 됩니다.']],
                ]
            ],
            [
                'order' => 2,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How is your daily diet?',
                    'vi' => 'Chế độ ăn uống hàng ngày của bạn ra sao?',
                    'de' => 'Wie ist Ihre tägliche Ernährung?',
                    'kr' => '당신의 식습관은 어떠신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Unhealthy, mostly fast food/sugar.', 'vi' => 'Không lành mạnh, toàn đồ nhanh/đường.', 'de' => 'Ungesund, meist Fast Food/Zucker.', 'kr' => '건강하지 않아요, 주로 패스트푸드나 설탕입니다.']],
                    ['score' => 2, 'content' => ['en' => 'Inconsistent, try but fail often.', 'vi' => 'Thất thường, cố gắng nhưng hay bỏ.', 'de' => 'Inkonsistent, versuche es oft, scheitere aber.', 'kr' => '일관성이 없어요, 노력하지만 자주 실패합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Balanced enough.', 'vi' => 'Khá cân bằng.', 'de' => 'Ausgewogen genug.', 'kr' => '충분히 균형 잡혀 있습니다.']],
                    ['score' => 4, 'content' => ['en' => 'Very healthy, nutritious and mindful.', 'vi' => 'Rất lành mạnh, bổ dưỡng và tỉnh thức.', 'de' => 'Sehr gesund, nahrhaft und achtsam.', 'kr' => '매우 건강하고 영양가 있으며 주의 깊습니다.']],
                ]
            ],
            [
                'order' => 3,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How often do you exercise?',
                    'vi' => 'Bạn tập thể dục bao lâu một lần?',
                    'de' => 'Wie oft treiben Sie Sport?',
                    'kr' => '운동을 얼마나 자주 하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Almost never.', 'vi' => 'Gần như không bao giờ.', 'de' => 'Fast nie.', 'kr' => '거의 하지 않습니다.']],
                    ['score' => 2, 'content' => ['en' => 'Rarely, once a month maybe.', 'vi' => 'Hiếm khi, chắc tháng 1 lần.', 'de' => 'Selten, vielleicht einmal im Monat.', 'kr' => '드물게, 아마 한 달에 한 번 정도.']],
                    ['score' => 3, 'content' => ['en' => 'Sometimes, 1-2 times a week.', 'vi' => 'Thỉnh thoảng, 1-2 lần/tuần.', 'de' => 'Manchmal, 1-2 Mal pro Woche.', 'kr' => '가끔, 일주일에 1-2회.']],
                    ['score' => 4, 'content' => ['en' => 'Regularly, 3+ times a week.', 'vi' => 'Đều đặn, trên 3 lần/tuần.', 'de' => 'Regelmäßig, 3+ Mal pro Woche.', 'kr' => '규칙적으로, 일주일에 3회 이상.']],
                ]
            ],
            [
                'order' => 4,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'Do you listen to your body signals (pain, fatigue)?',
                    'vi' => 'Bạn có lắng nghe tín hiệu cơ thể (đau, mệt) không?',
                    'de' => 'Hören Sie auf Ihre Körpersignale (Schmerz, Müdigkeit)?',
                    'kr' => '신체 신호(통증, 피로)에 귀를 기울이시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Ignore until I collapse.', 'vi' => 'Lờ đi cho đến khi sập nguồn.', 'de' => 'Ignorieren, bis ich zusammenbreche.', 'kr' => '쓰러질 때까지 무시합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Notice but often push through.', 'vi' => 'Có thấy nhưng hay cố quá.', 'de' => 'Bemerke es, mache aber oft weiter.', 'kr' => '알아차리지만 자주 무리합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Listen and rest sometimes.', 'vi' => 'Lắng nghe và nghỉ ngơi thỉnh thoảng.', 'de' => 'Höre zu und ruhe mich manchmal aus.', 'kr' => '듣고 가끔 휴식을 취합니다.']],
                    ['score' => 4, 'content' => ['en' => 'Always respect and care for my body.', 'vi' => 'Luôn tôn trọng và chăm sóc cơ thể.', 'de' => 'Respektiere und pflege meinen Körper immer.', 'kr' => '항상 내 몸을 존중하고 돌봅니다.']],
                ]
            ],
            [
                'order' => 5,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How is your energy level throughout the day?',
                    'vi' => 'Mức năng lượng trong ngày của bạn thế nào?',
                    'de' => 'Wie ist Ihr Energielevel den Tag über?',
                    'kr' => '하루 동안의 에너지 수준은 어떠신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Exhausted all the time.', 'vi' => 'Kiệt sức mọi lúc.', 'de' => 'Die ganze Zeit erschöpft.', 'kr' => '항상 지쳐 있습니다.']],
                    ['score' => 2, 'content' => ['en' => 'Low energy, need caffeine to survive.', 'vi' => 'Năng lượng thấp, cần cafein để sống.', 'de' => 'Wenig Energie, brauche Koffein zum Überleben.', 'kr' => '에너지가 낮아 생존을 위해 카페인이 필요합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Moderate, tired by afternoon.', 'vi' => 'Trung bình, mệt vào buổi chiều.', 'de' => 'Mittelmäßig, am Nachmittag müde.', 'kr' => '보통이며, 오후가 되면 피곤합니다.']],
                    ['score' => 4, 'content' => ['en' => 'High and stable.', 'vi' => 'Cao và ổn định.', 'de' => 'Hoch und stabil.', 'kr' => '높고 안정적입니다.']],
                ]
            ],
            [
                'order' => 6,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How is your breathing usually?',
                    'vi' => 'Hơi thở của bạn thường như thế nào?',
                    'de' => 'Wie ist Ihre Atmung normalerweise?',
                    'kr' => '평소 호흡은 어떠신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Short, shallow, and tense.', 'vi' => 'Ngắn, nông và căng thẳng.', 'de' => 'Kurz, flach und angespannt.', 'kr' => '짧고 얕으며 긴장되어 있습니다.']],
                    ['score' => 2, 'content' => ['en' => 'Often holding breath when stressed.', 'vi' => 'Hay nín thở khi căng thẳng.', 'de' => 'Halte oft den Atem an bei Stress.', 'kr' => '스트레스를 받으면 숨을 참습니다.']],
                    ['score' => 3, 'content' => ['en' => 'Normal, I don\'t notice it much.', 'vi' => 'Bình thường, tôi không để ý lắm.', 'de' => 'Normal, ich bemerke es nicht viel.', 'kr' => '보통이며, 크게 신경 쓰지 않습니다.']],
                    ['score' => 4, 'content' => ['en' => 'Deep, slow, and relaxed.', 'vi' => 'Sâu, chậm và thư giãn.', 'de' => 'Tief, langsam und entspannt.', 'kr' => '깊고 느리며 편안합니다.']],
                ]
            ],
            [
                'order' => 7,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How is your posture (sitting/standing)?',
                    'vi' => 'Tư thế (đứng/ngồi) của bạn thế nào?',
                    'de' => 'Wie ist Ihre Haltung (Sitzen/Stehen)?',
                    'kr' => '자세(앉기/서기)는 어떠신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Slouched, causing chronic pain.', 'vi' => 'Gù lưng, gây đau mãn tính.', 'de' => 'Gebückt, verursacht chronische Schmerzen.', 'kr' => '구부정하며 만성 통증을 유발합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Often slumped but try to correct.', 'vi' => 'Hay ngồi sai nhưng có cố sửa.', 'de' => 'Oft zusammengesackt, versuche aber zu korrigieren.', 'kr' => '자주 구부정하지만 고치려고 노력합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Generally okay.', 'vi' => 'Nhìn chung là ổn.', 'de' => 'Im Allgemeinen okay.', 'kr' => '대체로 괜찮습니다.']],
                    ['score' => 4, 'content' => ['en' => 'Upright, balanced, and confident.', 'vi' => 'Thẳng lưng, cân bằng và tự tin.', 'de' => 'Aufrecht, ausgeglichen und selbstbewusst.', 'kr' => '바르고 균형 잡혀 있으며 자신감이 있습니다.']],
                ]
            ],
            [
                'order' => 8,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'Do you drink enough water?',
                    'vi' => 'Bạn có uống đủ nước không?',
                    'de' => 'Trinken Sie genug Wasser?',
                    'kr' => '물을 충분히 마시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Rarely, mostly soda/coffee.', 'vi' => 'Hiếm khi, toàn uống nước ngọt/cafe.', 'de' => 'Selten, meist Limonade/Kaffee.', 'kr' => '거의 안 마시고 주로 탄산음료나 커피를 마십니다.']],
                    ['score' => 2, 'content' => ['en' => 'Not enough, often thirsty.', 'vi' => 'Không đủ, hay thấy khát.', 'de' => 'Nicht genug, oft durstig.', 'kr' => '충분하지 않고 자주 갈증을 느낍니다.']],
                    ['score' => 3, 'content' => ['en' => 'Yes, when I remember.', 'vi' => 'Có, khi nào nhớ ra thì uống.', 'de' => 'Ja, wenn ich daran denke.', 'kr' => '네, 기억날 때 마십니다.']],
                    ['score' => 4, 'content' => ['en' => 'Yes, consistently hydrated.', 'vi' => 'Có, luôn duy trì đủ nước.', 'de' => 'Ja, konstant hydriert.', 'kr' => '네, 꾸준히 수분을 섭취합니다.']],
                ]
            ],
            [
                'order' => 9,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How do you treat your body regarding rest?',
                    'vi' => 'Bạn đối xử với cơ thể thế nào về việc nghỉ ngơi?',
                    'de' => 'Wie behandeln Sie Ihren Körper in Bezug auf Ruhe?',
                    'kr' => '휴식과 관련하여 몸을 어떻게 대하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I push it to the limit daily.', 'vi' => 'Tôi bắt nó làm việc quá sức mỗi ngày.', 'de' => 'Ich gehe täglich ans Limit.', 'kr' => '매일 한계까지 몰아붙입니다.']],
                    ['score' => 2, 'content' => ['en' => 'I feel guilty when resting.', 'vi' => 'Tôi thấy tội lỗi khi nghỉ ngơi.', 'de' => 'Ich fühle mich schuldig beim Ausruhen.', 'kr' => '쉬는 것이 죄책감이 듭니다.']],
                    ['score' => 3, 'content' => ['en' => 'I rest when I finish work.', 'vi' => 'Hết việc thì tôi nghỉ.', 'de' => 'Ich ruhe mich aus, wenn ich fertig bin.', 'kr' => '일을 마치면 쉽니다.']],
                    ['score' => 4, 'content' => ['en' => 'I prioritize rest as much as work.', 'vi' => 'Tôi ưu tiên nghỉ ngơi ngang với làm việc.', 'de' => 'Ich priorisiere Ruhe genauso wie Arbeit.', 'kr' => '일만큼 휴식도 중요하게 생각합니다.']],
                ]
            ],
            [
                'order' => 10,
                'pillar_new' => 'body',
                'pillar_old' => 'heart',
                'content' => [
                    'en' => 'How often do you get sick (colds, flu, aches)?',
                    'vi' => 'Bạn có hay bị ốm vặt (cảm, cúm, đau nhức) không?',
                    'de' => 'Wie oft werden Sie krank (Erkältung, Grippe, Schmerzen)?',
                    'kr' => '얼마나 자주 아프신가요 (감기, 독감, 통증)?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Very often, weak immune system.', 'vi' => 'Rất thường xuyên, đề kháng yếu.', 'de' => 'Sehr oft, schwaches Immunsystem.', 'kr' => '매우 자주, 면역력이 약합니다.']],
                    ['score' => 2, 'content' => ['en' => 'A few times a year, seasonally.', 'vi' => 'Vài lần một năm, khi giao mùa.', 'de' => 'Ein paar Mal im Jahr, saisonal.', 'kr' => '일 년에 몇 번, 계절마다 아픕니다.']],
                    ['score' => 3, 'content' => ['en' => 'Rarely, usually recover quickly.', 'vi' => 'Hiếm khi, thường khỏi nhanh.', 'de' => 'Selten, erhole mich meist schnell.', 'kr' => '드물게, 대개 빨리 회복합니다.']],
                    ['score' => 4, 'content' => ['en' => 'Almost never, I feel very strong.', 'vi' => 'Gần như không, tôi thấy rất khỏe.', 'de' => 'Fast nie, ich fühle mich sehr stark.', 'kr' => '거의 없으며, 매우 건강하다고 느낍니다.']],
                ]
            ],
            // --- MIND (Questions 11-20) ---
            [
                'order' => 11,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How aware are you of your emotions right now?',
                    'vi' => 'Bạn nhận thức về cảm xúc của mình lúc này như thế nào?',
                    'de' => 'Wie bewusst sind Ihnen Ihre Emotionen gerade?',
                    'kr' => '지금 자신의 감정을 얼마나 잘 인지하고 계신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I feel numb or overwhelmed, can\'t name them.', 'vi' => 'Tôi thấy tê liệt hoặc quá tải, không gọi tên được.', 'de' => 'Ich fühle mich taub oder überwältigt, kann sie nicht benennen.', 'kr' => '무감각하거나 압도되어 감정을 이름 붙일 수 없습니다.']],
                    ['score' => 2, 'content' => ['en' => 'I realize them only after I react.', 'vi' => 'Tôi chỉ nhận ra sau khi đã phản ứng xong.', 'de' => 'Ich bemerke sie erst, nachdem ich reagiert habe.', 'kr' => '반응한 후에야 깨닫습니다.']],
                    ['score' => 3, 'content' => ['en' => 'I can name them but sometimes struggle.', 'vi' => 'Tôi có thể gọi tên nhưng đôi khi vẫn khó khăn.', 'de' => 'Ich kann sie benennen, habe aber manchmal Mühe.', 'kr' => '이름 붙일 수 있지만 때때로 어렵습니다.']],
                    ['score' => 4, 'content' => ['en' => 'I am fully aware and accepting of my emotions.', 'vi' => 'Tôi hoàn toàn nhận thức và chấp nhận cảm xúc của mình.', 'de' => 'Ich bin mir meiner Emotionen voll bewusst und akzeptiere sie.', 'kr' => '내 감정을 완전히 인지하고 받아들입니다.']],
                ]
            ],
            [
                'order' => 12,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'Do you often feel jealous of others?',
                    'vi' => 'Bạn có hay ghen tị với người khác không?',
                    'de' => 'Sind Sie oft eifersüchtig auf andere?',
                    'kr' => '다른 사람을 자주 질투하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Yes, I constantly compare and feel bitter.', 'vi' => 'Có, tôi liên tục so sánh và thấy cay đắng.', 'de' => 'Ja, ich vergleiche ständig und bin verbittert.', 'kr' => '네, 끊임없이 비교하며 씁쓸해합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Often, especially on social media.', 'vi' => 'Thường xuyên, nhất là trên mạng xã hội.', 'de' => 'Oft, besonders in sozialen Medien.', 'kr' => '자주, 특히 소셜 미디어에서 그렇습니다.']],
                    ['score' => 3, 'content' => ['en' => 'Sometimes, but I try to let it go.', 'vi' => 'Thỉnh thoảng, nhưng tôi cố gắng buông bỏ.', 'de' => 'Manchmal, aber ich versuche loszulassen.', 'kr' => '가끔 그렇지만 흘려보내려고 노력합니다.']],
                    ['score' => 4, 'content' => ['en' => 'Rarely, I celebrate others\' success.', 'vi' => 'Hiếm khi, tôi vui mừng cho thành công của người khác.', 'de' => 'Selten, ich feiere den Erfolg anderer.', 'kr' => '거의 없으며, 타인의 성공을 축하해 줍니다.']],
                ]
            ],
            [
                'order' => 13,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How do you handle anger?',
                    'vi' => 'Bạn xử lý cơn giận như thế nào?',
                    'de' => 'Wie gehen Sie mit Wut um?',
                    'kr' => '화를 어떻게 다스리나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I explode and say things I regret.', 'vi' => 'Tôi bùng nổ và nói những lời hối hận.', 'de' => 'Ich explodiere und sage Dinge, die ich bereue.', 'kr' => '폭발해서 후회할 말을 해버립니다.']],
                    ['score' => 2, 'content' => ['en' => 'I suppress it until I blow up later.', 'vi' => 'Tôi kìm nén cho đến khi nổ tung sau đó.', 'de' => 'Ich unterdrücke sie, bis ich später platze.', 'kr' => '나중에 터질 때까지 억누릅니다.']],
                    ['score' => 3, 'content' => ['en' => 'I walk away to cool down.', 'vi' => 'Tôi bỏ đi để bình tĩnh lại.', 'de' => 'Ich gehe weg, um mich abzukühlen.', 'kr' => '진정하기 위해 자리를 피합니다.']],
                    ['score' => 4, 'content' => ['en' => 'I observe the anger without reacting impulsively.', 'vi' => 'Tôi quan sát cơn giận mà không phản ứng bốc đồng.', 'de' => 'Ich beobachte die Wut, ohne impulsiv zu reagieren.', 'kr' => '충동적으로 반응하지 않고 화를 지켜봅니다.']],
                ]
            ],
            [
                'order' => 14,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How is your ability to focus?',
                    'vi' => 'Khả năng tập trung của bạn thế nào?',
                    'de' => 'Wie ist Ihre Konzentrationsfähigkeit?',
                    'kr' => '집중력은 어떠신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Very poor, easily distracted every few minutes.', 'vi' => 'Rất kém, cứ vài phút lại mất tập trung.', 'de' => 'Sehr schlecht, alle paar Minuten abgelenkt.', 'kr' => '매우 나빠서 몇 분마다 주의가 산만해집니다.']],
                    ['score' => 2, 'content' => ['en' => 'I multitask and get overwhelmed.', 'vi' => 'Tôi làm nhiều việc cùng lúc và bị quá tải.', 'de' => 'Ich mache Multitasking und bin überfordert.', 'kr' => '멀티태스킹을 하다가 압도됩니다.']],
                    ['score' => 3, 'content' => ['en' => 'Average, can focus when interested.', 'vi' => 'Bình thường, tập trung được khi thấy hứng thú.', 'de' => 'Durchschnittlich, kann mich konzentrieren bei Interesse.', 'kr' => '보통이며, 흥미가 있을 때 집중할 수 있습니다.']],
                    ['score' => 4, 'content' => ['en' => 'High, I can do Deep Work effectively.', 'vi' => 'Cao, tôi có thể làm việc sâu hiệu quả.', 'de' => 'Hoch, ich kann effektiv Deep Work machen.', 'kr' => '높아서 딥워크(Deep Work)를 효과적으로 할 수 있습니다.']],
                ]
            ],
            [
                'order' => 15,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How do you react to failure?',
                    'vi' => 'Bạn phản ứng thế nào với thất bại?',
                    'de' => 'Wie reagieren Sie auf Misserfolge?',
                    'kr' => '실패에 어떻게 반응하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I give up and feel worthless.', 'vi' => 'Tôi bỏ cuộc và thấy mình vô dụng.', 'de' => 'Ich gebe auf und fühle mich wertlos.', 'kr' => '포기하고 무가치하다고 느낍니다.']],
                    ['score' => 2, 'content' => ['en' => 'I blame others or circumstances.', 'vi' => 'Tôi đổ lỗi cho người khác hoặc hoàn cảnh.', 'de' => 'Ich beschuldige andere oder Umstände.', 'kr' => '남이나 상황을 탓합니다.']],
                    ['score' => 3, 'content' => ['en' => 'I feel sad but try again.', 'vi' => 'Tôi buồn nhưng sẽ thử lại.', 'de' => 'Ich bin traurig, versuche es aber erneut.', 'kr' => '슬프지만 다시 시도합니다.']],
                    ['score' => 4, 'content' => ['en' => 'I see it as a lesson and grow from it.', 'vi' => 'Tôi xem đó là bài học và trưởng thành hơn.', 'de' => 'Ich sehe es als Lektion und wachse daran.', 'kr' => '교훈으로 삼고 성장합니다.']],
                ]
            ],
            [
                'order' => 16,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How do you cope with stress?',
                    'vi' => 'Bạn đối phó với căng thẳng ra sao?',
                    'de' => 'Wie gehen Sie mit Stress um?',
                    'kr' => '스트레스에 어떻게 대처하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Substance use (alcohol, smoking) or escapism.', 'vi' => 'Dùng chất kích thích (rượu, thuốc) hoặc trốn chạy.', 'de' => 'Substanzkonsum (Alkohol, Rauchen) oder Flucht.', 'kr' => '물질(술, 담배)에 의존하거나 회피합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Overeating or sleeping excessively.', 'vi' => 'Ăn quá nhiều hoặc ngủ li bì.', 'de' => 'Übermäßiges Essen oder Schlafen.', 'kr' => '폭식하거나 과도하게 잠을 잡니다.']],
                    ['score' => 3, 'content' => ['en' => 'Talking to friends or distractions.', 'vi' => 'Nói chuyện với bạn bè hoặc tìm niềm vui khác.', 'de' => 'Mit Freunden reden oder Ablenkung.', 'kr' => '친구와 대화하거나 기분 전환을 합니다.']],
                    ['score' => 4, 'content' => ['en' => 'Meditation, exercise, or mindfulness.', 'vi' => 'Thiền, tập thể dục hoặc chánh niệm.', 'de' => 'Meditation, Sport oder Achtsamkeit.', 'kr' => '명상, 운동 또는 마음챙김을 합니다.']],
                ]
            ],
            [
                'order' => 17,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How often do you feel anxious without a clear reason?',
                    'vi' => 'Bạn có hay lo lắng vô cớ không?',
                    'de' => 'Wie oft fühlen Sie sich ohne klaren Grund ängstlich?',
                    'kr' => '뚜렷한 이유 없이 불안함을 자주 느끼시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Almost every day, constant worry.', 'vi' => 'Gần như mỗi ngày, lo âu thường trực.', 'de' => 'Fast jeden Tag, ständige Sorge.', 'kr' => '거의 매일, 끊임없이 걱정합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Frequently, hard to relax.', 'vi' => 'Thường xuyên, khó thư giãn.', 'de' => 'Häufig, schwer zu entspannen.', 'kr' => '자주 느끼며, 긴장을 풀기 어렵습니다.']],
                    ['score' => 3, 'content' => ['en' => 'Sometimes, when under pressure.', 'vi' => 'Thỉnh thoảng, khi bị áp lực.', 'de' => 'Manchmal, unter Druck.', 'kr' => '가끔, 압박감을 느낄 때 그렇습니다.']],
                    ['score' => 4, 'content' => ['en' => 'Rarely, I feel generally peaceful.', 'vi' => 'Hiếm khi, tôi thường thấy bình an.', 'de' => 'Selten, ich fühle mich generell friedlich.', 'kr' => '거의 없으며, 대체로 평온합니다.']],
                ]
            ],
            [
                'order' => 18,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'Do you practice gratitude daily?',
                    'vi' => 'Bạn có thực hành lòng biết ơn mỗi ngày không?',
                    'de' => 'Praktizieren Sie täglich Dankbarkeit?',
                    'kr' => '매일 감사함을 실천하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'No, I focus on what I lack.', 'vi' => 'Không, tôi toàn nghĩ về cái mình thiếu.', 'de' => 'Nein, ich konzentriere mich auf Mangel.', 'kr' => '아니요, 부족한 것에 집중합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Rarely.', 'vi' => 'Hiếm khi.', 'de' => 'Selten.', 'kr' => '드물게 합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Sometimes when good things happen.', 'vi' => 'Thỉnh thoảng khi có chuyện vui.', 'de' => 'Manchmal, wenn Gutes passiert.', 'kr' => '가끔 좋은 일이 있을 때 합니다.']],
                    ['score' => 4, 'content' => ['en' => 'Yes, it is a daily habit.', 'vi' => 'Có, đó là thói quen hàng ngày.', 'de' => 'Ja, es ist eine tägliche Gewohnheit.', 'kr' => '네, 매일 하는 습관입니다.']],
                ]
            ],
            [
                'order' => 19,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How confident are you in yourself?',
                    'vi' => 'Bạn tự tin vào bản thân đến mức nào?',
                    'de' => 'Wie selbstbewusst sind Sie?',
                    'kr' => '자신에 대해 얼마나 자신감이 있으신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Zero confidence, full of self-doubt.', 'vi' => 'Không chút nào, đầy nghi ngờ bản thân.', 'de' => 'Null Selbstvertrauen, voller Zweifel.', 'kr' => '전혀 없으며, 자기 의심으로 가득 차 있습니다.']],
                    ['score' => 2, 'content' => ['en' => 'Low, I need others\' approval.', 'vi' => 'Thấp, tôi cần người khác công nhận.', 'de' => 'Niedrig, brauche Zustimmung anderer.', 'kr' => '낮으며, 타인의 인정이 필요합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Average, depends on the situation.', 'vi' => 'Trung bình, tùy tình huống.', 'de' => 'Durchschnittlich, situationsabhängig.', 'kr' => '보통이며, 상황에 따라 다릅니다.']],
                    ['score' => 4, 'content' => ['en' => 'High, I trust my values and abilities.', 'vi' => 'Cao, tôi tin vào giá trị và năng lực của mình.', 'de' => 'Hoch, ich vertraue meinen Werten und Fähigkeiten.', 'kr' => '높으며, 나의 가치와 능력을 신뢰합니다.']],
                ]
            ],
            [
                'order' => 20,
                'pillar_new' => 'mind',
                'pillar_old' => 'grit',
                'content' => [
                    'en' => 'How healthy are your close relationships?',
                    'vi' => 'Các mối quan hệ thân thiết của bạn có lành mạnh không?',
                    'de' => 'Wie gesund sind Ihre engen Beziehungen?',
                    'kr' => '가까운 관계는 얼마나 건강한가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Toxic, draining, and conflict-ridden.', 'vi' => 'Độc hại, mệt mỏi và đầy xung đột.', 'de' => 'Toxisch, auslaugend und konfliktreich.', 'kr' => '유해하고 소모적이며 갈등이 많습니다.']],
                    ['score' => 2, 'content' => ['en' => 'Distant or superficial.', 'vi' => 'Xa cách hoặc hời hợt.', 'de' => 'Distanziert oder oberflächlich.', 'kr' => '소원하거나 피상적입니다.']],
                    ['score' => 3, 'content' => ['en' => 'Good, with occasional ups and downs.', 'vi' => 'Tốt, thỉnh thoảng có thăng trầm.', 'de' => 'Gut, mit gelegentlichen Höhen und Tiefen.', 'kr' => '좋으며, 가끔 기복이 있습니다.']],
                    ['score' => 4, 'content' => ['en' => 'Supportive, trusting, and loving.', 'vi' => 'Hỗ trợ, tin tưởng và yêu thương.', 'de' => 'Unterstützend, vertrauensvoll und liebevoll.', 'kr' => '지지와 신뢰, 사랑이 넘칩니다.']],
                ]
            ],
            // --- WISDOM (Questions 21-30) ---
            [
                'order' => 21,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'When something unlucky happens, what do you usually think?',
                    'vi' => 'Khi gặp chuyện xui xẻo, bạn thường nghĩ gì?',
                    'de' => 'Wenn etwas Unglückliches passiert, was denken Sie gewöhnlich?',
                    'kr' => '불행한 일이 생기면 보통 어떤 생각을 하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Why me? Life is unfair! (Blaming).', 'vi' => 'Tại sao lại là tôi? Đời thật bất công! (Đổ lỗi).', 'de' => 'Warum ich? Das Leben ist ungerecht! (Schuldzuweisung).', 'kr' => '왜 나야? 인생은 불공평해! (비난).']],
                    ['score' => 2, 'content' => ['en' => 'It\'s just bad luck or someone else\'s fault.', 'vi' => 'Chắc do xui hoặc do người khác.', 'de' => 'Es ist nur Pech oder die Schuld eines anderen.', 'kr' => '그냥 운이 나쁘거나 남의 탓이야.']],
                    ['score' => 3, 'content' => ['en' => 'I try to fix it but feel frustrated.', 'vi' => 'Tôi cố sửa nhưng vẫn thấy bực bội.', 'de' => 'Ich versuche es zu beheben, bin aber frustriert.', 'kr' => '고치려 노력하지만 좌절감을 느낍니다.']],
                    ['score' => 4, 'content' => ['en' => 'I accept it as cause and effect, and learn from it.', 'vi' => 'Tôi chấp nhận đó là nhân quả và học bài học từ nó.', 'de' => 'Ich akzeptiere es als Ursache und Wirkung und lerne daraus.', 'kr' => '인과관계로 받아들이고 그로부터 배웁니다.']],
                ]
            ],
            [
                'order' => 22,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'How do you understand "Happiness"?',
                    'vi' => 'Bạn hiểu thế nào về "Hạnh phúc"?',
                    'de' => 'Wie verstehen Sie "Glück"?',
                    'kr' => '"행복"을 어떻게 이해하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Having lots of money and luxury items.', 'vi' => 'Có nhiều tiền và đồ xa xỉ.', 'de' => 'Viel Geld und Luxusgüter haben.', 'kr' => '돈과 명품이 많은 것.']],
                    ['score' => 2, 'content' => ['en' => 'Stable job and good family.', 'vi' => 'Công việc ổn định và gia đình tốt.', 'de' => 'Stabiler Job und gute Familie.', 'kr' => '안정적인 직장과 좋은 가족.']],
                    ['score' => 3, 'content' => ['en' => 'Doing what I love.', 'vi' => 'Được làm điều mình thích.', 'de' => 'Tun, was ich liebe.', 'kr' => '내가 좋아하는 일을 하는 것.']],
                    ['score' => 4, 'content' => ['en' => 'Inner peace, independent of external conditions.', 'vi' => 'Sự bình an nội tâm, không phụ thuộc ngoại cảnh.', 'de' => 'Innerer Frieden, unabhängig von äußeren Bedingungen.', 'kr' => '외부 조건에 얽매이지 않는 내면의 평화.']],
                ]
            ],
            [
                'order' => 23,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'Do you believe in the law of cause and effect?',
                    'vi' => 'Bạn có tin vào luật nhân quả không?',
                    'de' => 'Glauben Sie an das Gesetz von Ursache und Wirkung?',
                    'kr' => '인과응보의 법칙을 믿으시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'No, success is about luck and trickery.', 'vi' => 'Không, thành công là do may mắn và mánh khóe.', 'de' => 'Nein, Erfolg hat mit Glück und Trickserei zu tun.', 'kr' => '아니요, 성공은 운과 속임수입니다.']],
                    ['score' => 2, 'content' => ['en' => 'Somewhat, "what goes around comes around".', 'vi' => 'Một chút, "gieo gió gặt bão".', 'de' => 'Einigermaßen, "wie man in den Wald hineinruft...".', 'kr' => '어느 정도는요, "뿌린 대로 거둔다".']],
                    ['score' => 3, 'content' => ['en' => 'Yes, mostly for big events.', 'vi' => 'Có, chủ yếu với các sự kiện lớn.', 'de' => 'Ja, meistens bei großen Ereignissen.', 'kr' => '네, 주로 큰 사건에 대해서는요.']],
                    ['score' => 4, 'content' => ['en' => 'Yes, every thought and action has consequences.', 'vi' => 'Có, mọi suy nghĩ và hành động đều có kết quả.', 'de' => 'Ja, jeder Gedanke und jede Handlung hat Konsequenzen.', 'kr' => '네, 모든 생각과 행동에는 결과가 따릅니다.']],
                ]
            ],
            [
                'order' => 24,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'What is your goal when arguing?',
                    'vi' => 'Mục tiêu của bạn khi tranh luận là gì?',
                    'de' => 'Was ist Ihr Ziel beim Streiten?',
                    'kr' => '논쟁할 때 당신의 목표는 무엇인가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'To win and prove I am right.', 'vi' => 'Thắng và chứng minh mình đúng.', 'de' => 'Gewinnen und beweisen, dass ich Recht habe.', 'kr' => '이기고 내가 옳음을 증명하는 것.']],
                    ['score' => 2, 'content' => ['en' => 'To defend my ego.', 'vi' => 'Bảo vệ cái tôi của mình.', 'de' => 'Mein Ego verteidigen.', 'kr' => '내 자존심을 지키는 것.']],
                    ['score' => 3, 'content' => ['en' => 'To find a middle ground.', 'vi' => 'Tìm tiếng nói chung.', 'de' => 'Einen Mittelweg finden.', 'kr' => '타협점을 찾는 것.']],
                    ['score' => 4, 'content' => ['en' => 'To discover the truth and learn.', 'vi' => 'Tìm ra sự thật và học hỏi.', 'de' => 'Die Wahrheit entdecken und lernen.', 'kr' => '진실을 발견하고 배우는 것.']],
                ]
            ],
            [
                'order' => 25,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'How do you view death or separation?',
                    'vi' => 'Bạn nhìn nhận cái chết hoặc sự chia ly thế nào?',
                    'de' => 'Wie sehen Sie Tod oder Trennung?',
                    'kr' => '죽음이나 이별을 어떻게 바라보시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Terrified, I avoid thinking about it.', 'vi' => 'Sợ hãi tột độ, tránh nghĩ về nó.', 'de' => 'Verängstigt, ich vermeide es, daran zu denken.', 'kr' => '두려워서 생각조차 피합니다.']],
                    ['score' => 2, 'content' => ['en' => 'Sad and anxious.', 'vi' => 'Buồn bã và lo âu.', 'de' => 'Traurig und ängstlich.', 'kr' => '슬프고 불안합니다.']],
                    ['score' => 3, 'content' => ['en' => 'It is a natural but painful part of life.', 'vi' => 'Tự nhiên nhưng đau đớn.', 'de' => 'Ein natürlicher, aber schmerzhafter Teil des Lebens.', 'kr' => '자연스럽지만 고통스러운 삶의 일부입니다.']],
                    ['score' => 4, 'content' => ['en' => 'Accept as impermanence, cherish the present.', 'vi' => 'Chấp nhận vô thường, trân trọng hiện tại.', 'de' => 'Als Vergänglichkeit akzeptieren, die Gegenwart schätzen.', 'kr' => '무상함으로 받아들이고 현재를 소중히 여깁니다.']],
                ]
            ],
            [
                'order' => 26,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'How honest are you with yourself?',
                    'vi' => 'Bạn trung thực với chính mình đến mức nào?',
                    'de' => 'Wie ehrlich sind Sie zu sich selbst?',
                    'kr' => '자신에게 얼마나 솔직하신가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I lie to myself to feel comfortable.', 'vi' => 'Tôi tự lừa dối để thấy thoải mái.', 'de' => 'Ich belüge mich selbst, um mich wohl zu fühlen.', 'kr' => '편안함을 위해 자신을 속입니다.']],
                    ['score' => 2, 'content' => ['en' => 'I ignore my flaws.', 'vi' => 'Tôi lờ đi khuyết điểm của mình.', 'de' => 'Ich ignoriere meine Fehler.', 'kr' => '나의 결점을 무시합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Honest mostly, but hide deep secrets.', 'vi' => 'Khá trung thực, nhưng giấu kín bí mật sâu.', 'de' => 'Meist ehrlich, verberge aber tiefe Geheimnisse.', 'kr' => '대체로 솔직하지만 깊은 비밀은 숨깁니다.']],
                    ['score' => 4, 'content' => ['en' => 'Brutally honest, I reflect to improve.', 'vi' => 'Rất trung thực, tôi soi chiếu để sửa mình.', 'de' => 'Brutal ehrlich, ich reflektiere, um mich zu verbessern.', 'kr' => '철저히 솔직하며, 개선을 위해 성찰합니다.']],
                ]
            ],
            [
                'order' => 27,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'What is the root cause of your suffering?',
                    'vi' => 'Nguyên nhân gốc rễ nỗi khổ của bạn là gì?',
                    'de' => 'Was ist die Wurzel Ihres Leidens?',
                    'kr' => '당신의 고통의 근본 원인은 무엇인가요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'Bad luck and bad people.', 'vi' => 'Vận đen và người xấu.', 'de' => 'Pech und schlechte Menschen.', 'kr' => '불운과 나쁜 사람들.']],
                    ['score' => 2, 'content' => ['en' => 'Lack of money/status.', 'vi' => 'Thiếu tiền/địa vị.', 'de' => 'Mangel an Geld/Status.', 'kr' => '돈이나 지위의 부족.']],
                    ['score' => 3, 'content' => ['en' => 'My own mistakes.', 'vi' => 'Lỗi lầm của chính tôi.', 'de' => 'Meine eigenen Fehler.', 'kr' => '내 자신의 실수.']],
                    ['score' => 4, 'content' => ['en' => 'My attachment and expectations.', 'vi' => 'Sự dính mắc và mong cầu của tôi.', 'de' => 'Meine Anhaftung und Erwartungen.', 'kr' => '나의 집착과 기대.']],
                ]
            ],
            [
                'order' => 28,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'How do you solve difficult problems?',
                    'vi' => 'Bạn giải quyết vấn đề khó khăn thế nào?',
                    'de' => 'Wie lösen Sie schwierige Probleme?',
                    'kr' => '어려운 문제를 어떻게 해결하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I panic and follow the crowd.', 'vi' => 'Tôi hoảng loạn và theo đám đông.', 'de' => 'Ich gerate in Panik und folge der Menge.', 'kr' => '당황해서 남들을 따라갑니다.']],
                    ['score' => 2, 'content' => ['en' => 'I rely on others to decide.', 'vi' => 'Tôi dựa vào người khác quyết định.', 'de' => 'Ich verlasse mich auf die Entscheidung anderer.', 'kr' => '남이 결정해주길 바랍니다.']],
                    ['score' => 3, 'content' => ['en' => 'I use logic and experience.', 'vi' => 'Tôi dùng logic và kinh nghiệm.', 'de' => 'Ich nutze Logik und Erfahrung.', 'kr' => '논리와 경험을 사용합니다.']],
                    ['score' => 4, 'content' => ['en' => 'I observe, analyze cause-effect deeply.', 'vi' => 'Tôi quan sát, phân tích nhân quả sâu sắc.', 'de' => 'Ich beobachte und analysiere Ursache und Wirkung tief.', 'kr' => '관찰하고 인과관계를 깊이 분석합니다.']],
                ]
            ],
            [
                'order' => 29,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'Do you keep learning new things?',
                    'vi' => 'Bạn có liên tục học hỏi điều mới không?',
                    'de' => 'Lernen Sie ständig Neues?',
                    'kr' => '새로운 것을 계속 배우시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'No, I know enough.', 'vi' => 'Không, tôi biết đủ rồi.', 'de' => 'Nein, ich weiß genug.', 'kr' => '아니요, 알 만큼 압니다.']],
                    ['score' => 2, 'content' => ['en' => 'Only for work requirements.', 'vi' => 'Chỉ khi công việc yêu cầu.', 'de' => 'Nur für Arbeitsanforderungen.', 'kr' => '업무상 필요할 때만요.']],
                    ['score' => 3, 'content' => ['en' => 'Sometimes read books/watch news.', 'vi' => 'Thỉnh thoảng đọc sách/xem tin.', 'de' => 'Manchmal Bücher lesen/Nachrichten schauen.', 'kr' => '가끔 책을 읽거나 뉴스를 봅니다.']],
                    ['score' => 4, 'content' => ['en' => 'Yes, lifelong learning is my way.', 'vi' => 'Có, học tập suốt đời là lối sống.', 'de' => 'Ja, lebenslanges Lernen ist mein Weg.', 'kr' => '네, 평생 학습이 제 방식입니다.']],
                ]
            ],
            [
                'order' => 30,
                'pillar_new' => 'wisdom',
                'pillar_old' => 'wisdom',
                'content' => [
                    'en' => 'Do you speak the truth?',
                    'vi' => 'Bạn có nói sự thật không?',
                    'de' => 'Sagen Sie die Wahrheit?',
                    'kr' => '진실을 말하시나요?',
                ],
                'options' => [
                    ['score' => 1, 'content' => ['en' => 'I lie frequently to gain advantage.', 'vi' => 'Tôi nói dối thường xuyên để có lợi.', 'de' => 'Ich lüge häufig, um Vorteile zu erlangen.', 'kr' => '이득을 위해 자주 거짓말을 합니다.']],
                    ['score' => 2, 'content' => ['en' => 'I tell white lies to avoid conflict.', 'vi' => 'Tôi nói dối vô hại để tránh xung đột.', 'de' => 'Ich erzähle Notlügen, um Konflikte zu vermeiden.', 'kr' => '갈등을 피하려 선의의 거짓말을 합니다.']],
                    ['score' => 3, 'content' => ['en' => 'Mostly truth, unless it hurts someone.', 'vi' => 'Đa phần là thật, trừ khi làm đau ai đó.', 'de' => 'Meistens die Wahrheit, es sei denn, es verletzt jemanden.', 'kr' => '누군가에게 상처가 되지 않는 한 대체로 진실을 말합니다.']],
                    ['score' => 4, 'content' => ['en' => 'Always truthful and beneficial.', 'vi' => 'Luôn trung thực và mang lại lợi ích.', 'de' => 'Immer wahrheitsgemäß und vorteilhaft.', 'kr' => '항상 진실하고 유익하게 말합니다.']],
                ]
            ],
        ];

        foreach ($questions as $qData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $qData['content'],
                'type' => 'single_choice',
                'order' => $qData['order'],
                'pillar_group' => $qData['pillar_old'],
                'pillar_group_new' => $qData['pillar_new'],
            ]);

            foreach ($qData['options'] as $optData) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $optData['content'],
                    'score' => $optData['score'],
                ]);
            }
        }
    }
}
