<?php

namespace Database\Seeders;

use App\Models\PainPoint;
use App\Models\MissionSet;
use App\Models\DailyMission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PainPointContentSeeder extends Seeder
{
    public function run(): void
    {
        $painPoints = PainPoint::all();

        foreach ($painPoints as $painPoint) {
            $this->command->info("Processing Pain Point: " . $painPoint->getTranslatedName('en'));
            
            $missionSet = $this->createMissionSetForPainPoint($painPoint);
            
            if ($missionSet) {
                $this->createDailyMissionsForSet($missionSet, $painPoint);
            }
        }
        
        $this->command->info("Pain Point content generation completed!");
    }

    private function createMissionSetForPainPoint(PainPoint $painPoint): ?MissionSet
    {
        $painPointName = $painPoint->getTranslatedName('en');
        $missionSetData = $this->getMissionSetData($painPointName);

        if (!$missionSetData) {
            return null;
        }

        return MissionSet::firstOrCreate([
            'name->en' => $missionSetData['name']['en']
        ], [
            'name' => $missionSetData['name'],
            'description' => $missionSetData['description'],
            'type' => 'pain_point',
            'created_by' => 1, // System user
            'is_default' => true,
        ]);
    }

    private function createDailyMissionsForSet(MissionSet $missionSet, PainPoint $painPoint): void
    {
        $painPointName = $painPoint->getTranslatedName('en');
        $missions = $this->getMissionsForPainPoint($painPointName);

        foreach ($missions as $dayNumber => $missionData) {
            DailyMission::firstOrCreate([
                'mission_set_id' => $missionSet->id,
                'day_number' => $dayNumber
            ], [
                'title' => $missionData['title'],
                'description' => $missionData['description'],
                'points' => $missionData['points'],
                'is_body' => $missionData['is_body'],
                'is_mind' => $missionData['is_mind'],
                'is_wisdom' => $missionData['is_wisdom'],
                'created_by_id' => 1, // System user
            ]);
        }
    }

    private function getMissionSetData(string $painPointName): ?array
    {
        $missionSets = [
            'Nóng giận mất kiểm soát' => [
                'name' => [
                    'en' => 'Anger Management 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày kiểm soát cơn giận',
                    'de' => '14-Tage-Wutmanagement-Programm',
                    'kr' => '분노 관리 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Learn to control your anger and find inner peace through daily practices',
                    'vi' => 'Học cách kiểm soát cơn giận và tìm thấy sự bình yên nội tâm qua thực hành hàng ngày',
                    'de' => 'Lernen Sie, Ihre Wut zu kontrollieren und durch tägliche Übungen innere Ruhe zu finden',
                    'kr' => '매일 연습을 통해 분노를 통제하고 내면의 평화를 찾으세요'
                ]
            ],
            'Mất kết nối với con cái' => [
                'name' => [
                    'en' => 'Parent-Child Reconnection 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày kết nối lại với con cái',
                    'de' => '14-Tage-Eltern-Kind-Wiederanbindungsprogramm',
                    'kr' => '부모-자녀 재연결 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Rebuild your relationship with your children through understanding and communication',
                    'vi' => 'Tái xây dựng mối quan hệ với con cái qua sự thấu hiểu và giao tiếp',
                    'de' => 'Bauen Sie Ihre Beziehung zu Ihren Kindern durch Verständnis und Kommunikation wieder auf',
                    'kr' => '이해와 소통을 통해 자녀와의 관계를 재건하세요'
                ]
            ],
            'Hôn nhân rạn nứt' => [
                'name' => [
                    'en' => 'Marriage Repair 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày hàn gắn hôn nhân',
                    'de' => '14-Tage-Ehe-Reparaturprogramm',
                    'kr' => '결혼 복구 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Heal your marriage and rebuild trust with your partner',
                    'vi' => 'Chữa lành hôn nhân và xây dựng lại sự tin tưởng với người bạn đời',
                    'de' => 'Heilen Sie Ihre Ehe und bauen Sie Vertrauen mit Ihrem Partner wieder auf',
                    'kr' => '결혼 생활을 치유하고 파트너와의 신뢰를 재구축하세요'
                ]
            ],
            'Cô đơn, Trống rỗng' => [
                'name' => [
                    'en' => 'Overcoming Loneliness 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày vượt qua cô đơn',
                    'de' => '14-Tage-Einsamkeitsüberwindungsprogramm',
                    'kr' => '외로움 극복 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Find connection and meaning in your life through daily practices',
                    'vi' => 'Tìm thấy sự kết nối và ý nghĩa trong cuộc sống qua thực hành hàng ngày',
                    'de' => 'Finden Sie durch tägliche Übungen Verbindung und Sinn in Ihrem Leben',
                    'kr' => '매일의 실천을 통해 삶의 연결과 의미를 찾으세요'
                ]
            ],
            'Ghen tuông, Đố kỵ' => [
                'name' => [
                    'en' => 'Overcoming Jealousy 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày vượt qua ghen tuông',
                    'de' => '14-Tage-Eifersuchtüberwindungsprogramm',
                    'kr' => '질투 극복 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Transform jealousy into personal growth and self-confidence',
                    'vi' => 'Biến ghen tuông thành sự phát triển cá nhân và sự tự tin',
                    'de' => 'Verwandeln Sie Eifersucht in persönliches Wachstum und Selbstvertrauen',
                    'kr' => '질투를 개인적 성장과 자신감으로 변화시키세요'
                ]
            ],
            'Tổn thương quá khứ' => [
                'name' => [
                    'en' => 'Healing Past Trauma 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày chữa lành tổn thương quá khứ',
                    'de' => '14-Tage-Heilung-von-Vergangenheitstrauma-Programm',
                    'kr' => '과거 트라우마 치유 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Release past pain and embrace a hopeful future',
                    'vi' => 'Giải tỏa nỗi đau quá khứ và ôm lấy tương lai hy vọng',
                    'de' => 'Lösen Sie vergangene Schmerzen und umarmen Sie eine hoffnungsvolle Zukunft',
                    'kr' => '과거의 고통을 풀고 희망찬 미래를 받아들이세요'
                ]
            ],
            'Mất định hướng cuộc đời' => [
                'name' => [
                    'en' => 'Life Purpose Discovery 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày khám phá mục đích sống',
                    'de' => '14-Tage-Lebenszweck-Entdeckungsprogramm',
                    'kr' => '인생 목적 발견 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Discover your true purpose and create a meaningful life',
                    'vi' => 'Khám phá mục đích đích thực của bạn và tạo ra một cuộc sống ý nghĩa',
                    'de' => 'Entdecken Sie Ihren wahren Zweck und schaffen Sie ein bedeutungsvolles Leben',
                    'kr' => '당신의 진정한 목적을 발견하고 의미 있는 삶을 만드세요'
                ]
            ],
            'Chán nản công việc' => [
                'name' => [
                    'en' => 'Career Renewal 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày tái tạo sự nghiệp',
                    'de' => '14-Tage-Karriere-Erneuerungsprogramm',
                    'kr' => '커리어 갱신 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Rediscover passion and purpose in your professional life',
                    'vi' => 'Tái khám phá đam mê và mục đích trong đời sống chuyên môn của bạn',
                    'de' => 'Entdecken Sie Leidenschaft und Zweck in Ihrem Berufsleben wieder',
                    'kr' => '전문적인 삶에서 열정과 목적을 재발견하세요'
                ]
            ],
            'Áp lực Nợ nần/Tài chính' => [
                'name' => [
                    'en' => 'Financial Freedom 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày tự do tài chính',
                    'de' => '14-Tage-Finanzielle-Freiheit-Programm',
                    'kr' => '재정적 자유 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Transform your relationship with money and build financial security',
                    'vi' => 'Biến đổi mối quan hệ của bạn với tiền bạc và xây dựng an ninh tài chính',
                    'de' => 'Verwandeln Sie Ihre Beziehung zu Geld und bauen Sie finanzielle Sicherheit auf',
                    'kr' => '돈과의 관계를 변화시키고 재정적 안정을 구축하세요'
                ]
            ],
            'Sợ thất bại, Thiếu tự tin' => [
                'name' => [
                    'en' => 'Confidence Building 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày xây dựng sự tự tin',
                    'de' => '14-Tage-Vertrauensaufbauprogramm',
                    'kr' => '자신감 구축 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Build unshakeable confidence and overcome fear of failure',
                    'vi' => 'Xây dựng sự tự tin không thể lay chuyển và vượt qua nỗi sợ thất bại',
                    'de' => 'Bauen Sie unerschütterliches Vertrauen auf und überwinden Sie die Angst vor Versagen',
                    'kr' => '흔들리지 않는 자신감을 구축하고 실패에 대한 두려움을 극복하세요'
                ]
            ],
            'Nghiện mê tín dị đoan' => [
                'name' => [
                    'en' => 'Rational Living 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày sống lý trí',
                    'de' => '14-Tage-Rationales-Leben-Programm',
                    'kr' => '이성적 삶 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Replace superstition with rational thinking and personal empowerment',
                    'vi' => 'Thay thế mê tín dị đoan bằng tư duy lý trí và trao quyền cá nhân',
                    'de' => 'Ersetzen Sie Aberglauben durch rationales Denken und persönliche Stärkung',
                    'kr' => '미신을 이성적 사고와 개인적 권한으로 대체하세요'
                ]
            ],
            'Mất ngủ triền miên' => [
                'name' => [
                    'en' => 'Sleep Recovery 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày phục hồi giấc ngủ',
                    'de' => '14-Tage-Schlafwiederherstellungsprogramm',
                    'kr' => '수면 회복 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Restore natural sleep patterns and wake up refreshed',
                    'vi' => 'Khôi phục chu kỳ ngủ tự nhiên và thức dậy sảng khoái',
                    'de' => 'Stellen Sie natürliche Schlafmuster wieder her und wachen Sie erholt auf',
                    'kr' => '자연적인 수면 패턴을 회복하고 상쾌하게 일어나세요'
                ]
            ],
            'Nghiện Mạng xã hội/Game' => [
                'name' => [
                    'en' => 'Digital Detox 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày giải độc số',
                    'de' => '14-Tage-Digital-Detox-Programm',
                    'kr' => '디지털 디톡스 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Break free from digital addiction and reclaim your time',
                    'vi' => 'Giải phóng khỏi nghiện kỹ thuật số và giành lại thời gian của bạn',
                    'de' => 'Befreien Sie sich von digitaler Sucht und gewinnen Sie Ihre Zeit zurück',
                    'kr' => '디지털 중독에서 벗어나 시간을 되찾으세요'
                ]
            ],
            'Trì hoãn, Lười biếng' => [
                'name' => [
                    'en' => 'Productivity Mastery 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày làm chủ năng suất',
                    'de' => '14-Tage-Produktivitätsmeisterschaftsprogramm',
                    'kr' => '생산성 마스터리 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Overcome procrastination and build lasting productive habits',
                    'vi' => 'Vượt qua sự trì hoãn và xây dựng thói quen sản xuất bền vững',
                    'de' => 'Überwinden Sie Prokrastination und bauen Sie produktive Gewohnheiten auf',
                    'kr' => '미루는 습관을 극복하고 지속적인 생산적인 습관을 구축하세요'
                ]
            ],
            'Stress, Căng thẳng tột độ' => [
                'name' => [
                    'en' => 'Stress Relief 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày giảm căng thẳng',
                    'de' => '14-Tage-Stressabbau-Programm',
                    'kr' => '스트레스 완화 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Manage stress effectively and find inner calm',
                    'vi' => 'Quản lý căng thẳng hiệu quả và tìm thấy sự bình yên nội tâm',
                    'de' => 'Managen Sie Stress effektiv und finden Sie innere Ruhe',
                    'kr' => '스트레스를 효과적으로 관리하고 내면의 평온을 찾으세요'
                ]
            ],
            'Sức khỏe suy kiệt' => [
                'name' => [
                    'en' => 'Health Restoration 14-Day Program',
                    'vi' => 'Lộ trình 14 ngày phục hồi sức khỏe',
                    'de' => '14-Tage-Gesundheitswiederherstellungsprogramm',
                    'kr' => '건강 회복 14일 프로그램'
                ],
                'description' => [
                    'en' => 'Rebuild your physical health and vitality',
                    'vi' => 'Tái xây dựng sức khỏe thể chất và sự sống động của bạn',
                    'de' => 'Bauen Sie Ihre körperliche Gesundheit und Vitalität wieder auf',
                    'kr' => '신체 건강과 활력을 재구축하세요'
                ]
            ]
        ];

        return $missionSets[$painPointName] ?? null;
    }

    private function getMissionsForPainPoint(string $painPointName): array
    {
        $missions = [
            'Mất ngủ triền miên' => [
                1 => [
                    'title' => [
                        'en' => 'No Screen Time Before Bed',
                        'vi' => 'Không sử dụng thiết bị điện tử trước khi ngủ',
                        'de' => 'Keine Bildschirmzeit vor dem Schlafengehen',
                        'kr' => '잠자리에 들기 전 화면 시간 없음'
                    ],
                    'description' => [
                        'en' => 'Avoid all screens (phone, TV, computer) for 1 hour before bedtime',
                        'vi' => 'Tránh tất cả các thiết bị điện tử trong 1 giờ trước khi ngủ',
                        'de' => 'Vermeiden Sie alle Bildschirme 1 Stunde vor dem Schlafengehen',
                        'kr' => '취침 1시간 전 모든 화면 피하기'
                    ],
                    'points' => 10,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                2 => [
                    'title' => [
                        'en' => 'Evening Meditation',
                        'vi' => 'Thiền buổi tối',
                        'de' => 'Abendmeditation',
                        'kr' => '저녁 명상'
                    ],
                    'description' => [
                        'en' => 'Practice 10 minutes of calming meditation before sleep',
                        'vi' => 'Thực hành 10 phút thiền định thư giãn trước khi ngủ',
                        'de' => 'Üben Sie 10 Minuten beruhigende Meditation vor dem Schlaf',
                        'kr' => '잠들기 전 10분의 진정 명상 실천'
                    ],
                    'points' => 15,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                3 => [
                    'title' => [
                        'en' => 'Consistent Sleep Schedule',
                        'vi' => 'Lịch ngủ đều đặn',
                        'de' => 'Konsistenter Schlafrhythmus',
                        'kr' => '일관된 수면 스케줄'
                    ],
                    'description' => [
                        'en' => 'Go to bed and wake up at the same time every day',
                        'vi' => 'Đi ngủ và thức dậy vào cùng một thời điểm mỗi ngày',
                        'de' => 'Gehen Sie jeden Tag zur gleichen Zeit ins Bett und stehen Sie auf',
                        'kr' => '매일 같은 시간에 잠자리에 들고 일어나세요'
                    ],
                    'points' => 20,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                4 => [
                    'title' => [
                        'en' => 'No Caffeine After 2 PM',
                        'vi' => 'Không dùng caffeine sau 2 giờ chiều',
                        'de' => 'Kein Koffein nach 14 Uhr',
                        'kr' => '오후 2시 이후 카페인 없음'
                    ],
                    'description' => [
                        'en' => 'Avoid coffee, tea, and other caffeinated drinks in the afternoon',
                        'vi' => 'Tránh cà phê, trà và đồ uống có caffeine khác vào buổi chiều',
                        'de' => 'Vermeiden Sie Kaffee, Tee und andere koffeinhaltige Getränke am Nachmittag',
                        'kr' => '오후에는 커피, 차 및 기타 카페인 음료 피하기'
                    ],
                    'points' => 10,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                5 => [
                    'title' => [
                        'en' => 'Bedroom Environment',
                        'vi' => 'Môi trường phòng ngủ',
                        'de' => 'Schlafzimmerumgebung',
                        'kr' => '침실 환경'
                    ],
                    'description' => [
                        'en' => 'Keep your bedroom cool, dark, and quiet for optimal sleep',
                        'vi' => 'Giữ phòng ngủ mát mẻ, tối và yên tĩnh để ngủ ngon nhất',
                        'de' => 'Halten Sie Ihr Schlafzimmer kühl, dunkel und ruhig für optimalen Schlaf',
                        'kr' => '최적의 수면을 위해 침실을 시원하고 어둡고 조용하게 유지하세요'
                    ],
                    'points' => 15,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                6 => [
                    'title' => [
                        'en' => 'Progressive Muscle Relaxation',
                        'vi' => 'Thư giãn cơ tiến bộ',
                        'de' => 'Progressive Muskelentspannung',
                        'kr' => '진보적 근육 이완'
                    ],
                    'description' => [
                        'en' => 'Practice tensing and relaxing muscle groups to release physical tension',
                        'vi' => 'Thực hành siết chặt và thư giãn các nhóm cơ để giải tỏa căng thẳng thể chất',
                        'de' => 'Üben Sie das Anspannen und Entspannen von Muskelgruppen zur körperlichen Entspannung',
                        'kr' => '신체 긴장을 풀기 위해 근육 그룹의 긴장과 이완 연습'
                    ],
                    'points' => 15,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                7 => [
                    'title' => [
                        'en' => 'Sleep Journal',
                        'vi' => 'Nhật ký giấc ngủ',
                        'de' => 'Schlafjournal',
                        'kr' => '수면 일기'
                    ],
                    'description' => [
                        'en' => 'Track your sleep patterns and note what affects your sleep quality',
                        'vi' => 'Theo dõi chu kỳ ngủ của bạn và ghi chú những gì ảnh hưởng đến chất lượng giấc ngủ',
                        'de' => 'Verfolgen Sie Ihre Schlafmuster und notieren Sie, was Ihre Schlafqualität beeinflusst',
                        'kr' => '수면 패턴을 추적하고 수면의 질에 영향을 미치는 요소 기록'
                    ],
                    'points' => 10,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ]
            ],
            'Stress, Căng thẳng tột độ' => [
                1 => [
                    'title' => [
                        'en' => 'Deep Breathing Exercise',
                        'vi' => 'Bài tập hít thở sâu',
                        'de' => 'Tiefenatmungsübung',
                        'kr' => '깊은 호흡 운동'
                    ],
                    'description' => [
                        'en' => 'Practice 5 minutes of deep breathing to calm your nervous system',
                        'vi' => 'Thực hành 5 phút hít thở sâu để làm dịu hệ thần kinh',
                        'de' => 'Üben Sie 5 Minuten Tiefenatmung zur Beruhigung Ihres Nervensystems',
                        'kr' => '신경계를 진정시키기 위해 5분간 깊은 호흡 실천'
                    ],
                    'points' => 10,
                    'is_body' => true,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                2 => [
                    'title' => [
                        'en' => 'Morning Mindfulness',
                        'vi' => 'Chánh niệm buổi sáng',
                        'de' => 'Morgens Achtsamkeit',
                        'kr' => '아침 마음챙김'
                    ],
                    'description' => [
                        'en' => 'Start your day with 10 minutes of mindful observation',
                        'vi' => 'Bắt đầu ngày mới với 10 phút quan sát chánh niệm',
                        'de' => 'Beginnen Sie Ihren Tag mit 10 Minuten achtsamer Beobachtung',
                        'kr' => '10분의 마음챙김 관찰로 하루 시작'
                    ],
                    'points' => 15,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                3 => [
                    'title' => [
                        'en' => 'Stress-Free Walk',
                        'vi' => 'Đi bộ không căng thẳng',
                        'de' => 'Stressfreier Spaziergang',
                        'kr' => '스트레스 없는 산책'
                    ],
                    'description' => [
                        'en' => 'Take a 20-minute walk without phone or distractions',
                        'vi' => 'Đi bộ 20 phút không dùng điện thoại hoặc sao nhãng',
                        'de' => 'Machen Sie einen 20-minütigen Spaziergang ohne Handy oder Ablenkungen',
                        'kr' => '전화나 방해 없이 20분 산책'
                    ],
                    'points' => 20,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                4 => [
                    'title' => [
                        'en' => 'Gratitude Practice',
                        'vi' => 'Thực hành lòng biết ơn',
                        'de' => 'Dankbarkeitspraxis',
                        'kr' => '감사 실천'
                    ],
                    'description' => [
                        'en' => 'Write down 3 things you are grateful for today',
                        'vi' => 'Viết ra 3 điều bạn biết ơn hôm nay',
                        'de' => 'Schreiben Sie 3 Dinge auf, für die Sie heute dankbar sind',
                        'kr' => '오늘 감사한 3가지 적기'
                    ],
                    'points' => 10,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                5 => [
                    'title' => [
                        'en' => 'Progressive Relaxation',
                        'vi' => 'Thư giãn tiến bộ',
                        'de' => 'Progressive Entspannung',
                        'kr' => '진보적 이완'
                    ],
                    'description' => [
                        'en' => 'Release tension from each muscle group systematically',
                        'vi' => 'Giải tỏa căng thẳng từ mỗi nhóm cơ một cách có hệ thống',
                        'de' => 'Lösen Sie systematisch Spannung von jeder Muskelgruppe',
                        'kr' => '각 근육 그룹의 긴장을 체계적으로 풀기'
                    ],
                    'points' => 15,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                6 => [
                    'title' => [
                        'en' => 'Nature Connection',
                        'vi' => 'Kết nối thiên nhiên',
                        'de' => 'Naturverbindung',
                        'kr' => '자연 연결'
                    ],
                    'description' => [
                        'en' => 'Spend 15 minutes in nature or looking at natural scenes',
                        'vi' => 'Dành 15 phút trong thiên nhiên hoặc ngắm nhìn cảnh tự nhiên',
                        'de' => 'Verbringen Sie 15 Minuten in der Natur oder betrachten Sie Naturszenen',
                        'kr' => '자연에서 15분 보내거나 자연 경관 관찰'
                    ],
                    'points' => 15,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                7 => [
                    'title' => [
                        'en' => 'Digital Detox Hour',
                        'vi' => 'Giải độc số một giờ',
                        'de' => 'Digitale Detox-Stunde',
                        'kr' => '디지털 디톡스 시간'
                    ],
                    'description' => [
                        'en' => 'Take one hour completely away from all digital devices',
                        'vi' => 'Dành một giờ hoàn toàn远离 tất cả thiết bị kỹ thuật số',
                        'de' => 'Verbringen Sie eine Stunde komplett ohne digitale Geräte',
                        'kr' => '모든 디지털 기기 없이 한 시간 보내기'
                    ],
                    'points' => 20,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ]
            ],
            'Nghiện Mạng xã hội/Game' => [
                1 => [
                    'title' => [
                        'en' => 'Screen Time Tracker',
                        'vi' => 'Theo dõi thời gian màn hình',
                        'de' => 'Bildschirmzeit-Tracker',
                        'kr' => '화면 시간 추적기'
                    ],
                    'description' => [
                        'en' => 'Monitor your daily screen time and set limits',
                        'vi' => 'Theo dõi thời gian sử dụng màn hình hàng ngày và đặt giới hạn',
                        'de' => 'Überwachen Sie Ihre tägliche Bildschirmzeit und setzen Sie Grenzen',
                        'kr' => '일일 화면 시간을 모니터링하고 한도 설정'
                    ],
                    'points' => 10,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                2 => [
                    'title' => [
                        'en' => 'No Phone First Hour',
                        'vi' => 'Không điện thoại giờ đầu tiên',
                        'de' => 'Kein Handy in der ersten Stunde',
                        'kr' => '첫 한 시간 동안 전화 없음'
                    ],
                    'description' => [
                        'en' => 'Start your day without checking your phone for the first hour',
                        'vi' => 'Bắt đầu ngày mới không kiểm tra điện thoại trong giờ đầu tiên',
                        'de' => 'Beginnen Sie Ihren Tag ohne Handy in der ersten Stunde',
                        'kr' => '첫 한 시간 동안 전화 없이 하루 시작'
                    ],
                    'points' => 15,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                3 => [
                    'title' => [
                        'en' => 'Real World Activity',
                        'vi' => 'Hoạt động thế giới thực',
                        'de' => 'Aktivität in der realen Welt',
                        'kr' => '실세계 활동'
                    ],
                    'description' => [
                        'en' => 'Replace 30 minutes of screen time with a physical activity',
                        'vi' => 'Thay thế 30 phút thời gian màn hình bằng hoạt động thể chất',
                        'de' => 'Ersetzen Sie 30 Minuten Bildschirmzeit durch eine körperliche Aktivität',
                        'kr' => '30분의 화면 시간을 신체 활동으로 대체'
                    ],
                    'points' => 20,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ],
                4 => [
                    'title' => [
                        'en' => 'Social Media Cleanse',
                        'vi' => 'Làm sạch mạng xã hội',
                        'de' => 'Social-Media-Entgiftung',
                        'kr' => '소셜 미디어 클렌즈'
                    ],
                    'description' => [
                        'en' => 'Delete one social media app or unfollow toxic accounts',
                        'vi' => 'Xóa một ứng dụng mạng xã hội hoặc hủy theo dõi tài khoản độc hại',
                        'de' => 'Löschen Sie eine Social-Media-App oder entfolgen Sie toxische Accounts',
                        'kr' => '소셜 미디어 앱 하나 삭제 또는 유해한 계정 언팔로우'
                    ],
                    'points' => 15,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                5 => [
                    'title' => [
                        'en' => 'Face-to-Face Conversation',
                        'vi' => 'Trò chuyện trực tiếp',
                        'de' => 'Gesicht-zu-Gesicht-Gespräch',
                        'kr' => '얼굴 맞대화 대화'
                    ],
                    'description' => [
                        'en' => 'Have a meaningful conversation with someone in person',
                        'vi' => 'Có một cuộc trò chuyện ý nghĩa với ai đó trực tiếp',
                        'de' => 'Führen Sie ein bedeutungsvolles Gespräch mit jemandem persönlich',
                        'kr' => '누군가와 의미 있는 대화를 직접 나누기'
                    ],
                    'points' => 20,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                6 => [
                    'title' => [
                        'en' => 'Hobby Development',
                        'vi' => 'Phát triển sở thích',
                        'de' => 'Hobby-Entwicklung',
                        'kr' => '취미 개발'
                    ],
                    'description' => [
                        'en' => 'Spend 30 minutes on a non-digital hobby or skill',
                        'vi' => 'Dành 30 phút cho sở thích hoặc kỹ năng không kỹ thuật số',
                        'de' => 'Verbringen Sie 30 Minuten mit einem nicht-digitalen Hobby oder Skill',
                        'kr' => '디지털이 아닌 취미나 기술에 30분 투자'
                    ],
                    'points' => 15,
                    'is_body' => false,
                    'is_mind' => true,
                    'is_wisdom' => false
                ],
                7 => [
                    'title' => [
                        'en' => 'Digital Sunset',
                        'vi' => 'Hoàng hôn kỹ thuật số',
                        'de' => 'Digitaler Sonnenuntergang',
                        'kr' => '디지털 일몰'
                    ],
                    'description' => [
                        'en' => 'Turn off all screens 2 hours before bedtime',
                        'vi' => 'Tắt tất cả các màn hình 2 giờ trước khi ngủ',
                        'de' => 'Schalten Sie alle Bildschirme 2 Stunden vor dem Schlafengehen aus',
                        'kr' => '취침 2시간 전 모든 화면 끄기'
                    ],
                    'points' => 20,
                    'is_body' => true,
                    'is_mind' => false,
                    'is_wisdom' => false
                ]
            ]
        ];

        // Default missions for pain points without specific content
        $defaultMissions = [
            1 => [
                'title' => [
                    'en' => 'Self-Reflection Day',
                    'vi' => 'Ngày tự phản tư',
                    'de' => 'Tag der Selbstreflexion',
                    'kr' => '자기 성찰의 날'
                ],
                'description' => [
                    'en' => 'Take time to understand your current challenges and feelings',
                    'vi' => 'Dành thời gian để hiểu những thách thức và cảm xúc hiện tại của bạn',
                    'de' => 'Nehmen Sie sich Zeit, Ihre aktuellen Herausforderungen und Gefühle zu verstehen',
                    'kr' => '현재의 도전과 감정을 이해할 시간을 가지세요'
                ],
                'points' => 10,
                'is_body' => false,
                'is_mind' => true,
                'is_wisdom' => false
            ],
            2 => [
                'title' => [
                    'en' => 'Small Step Forward',
                    'vi' => 'Bước tiến nhỏ',
                    'de' => 'Kleiner Schritt nach vorne',
                    'kr' => '작은 전진'
                ],
                'description' => [
                    'en' => 'Take one small action toward addressing your pain point',
                    'vi' => 'Thực hiện một hành động nhỏ để giải quyết điểm đau của bạn',
                    'de' => 'Unternehmen Sie eine kleine Aktion zur Bewältigung Ihres Schmerzpunktes',
                    'kr' => '고통 지점을 해결하기 위한 작은 행동을 취하세요'
                ],
                'points' => 15,
                'is_body' => false,
                'is_mind' => true,
                'is_wisdom' => false
            ],
            3 => [
                'title' => [
                    'en' => 'Seek Support',
                    'vi' => 'Tìm kiếm sự hỗ trợ',
                    'de' => 'Unterstützung suchen',
                    'kr' => '지지 찾기'
                ],
                'description' => [
                    'en' => 'Reach out to someone you trust for support or guidance',
                    'vi' => 'Liên hệ với ai đó bạn tin tưởng để tìm sự hỗ trợ hoặc hướng dẫn',
                    'de' => 'Wenden Sie sich an jemanden, dem Sie vertrauen, für Unterstützung oder Anleitung',
                    'kr' => '지원이나 지도를 위해 신뢰하는 사람에게 연락하세요'
                ],
                'points' => 15,
                'is_body' => false,
                'is_mind' => true,
                'is_wisdom' => false
            ],
            4 => [
                'title' => [
                    'en' => 'Mindful Practice',
                    'vi' => 'Thực hành chánh niệm',
                    'de' => 'Achtsame Praxis',
                    'kr' => '마음챙김 실천'
                ],
                'description' => [
                    'en' => 'Practice 10 minutes of mindfulness or meditation',
                    'vi' => 'Thực hành 10 phút chánh niệm hoặc thiền định',
                    'de' => 'Üben Sie 10 Minuten Achtsamkeit oder Meditation',
                    'kr' => '10분의 마음챙김이나 명상 실천'
                ],
                'points' => 15,
                'is_body' => false,
                'is_mind' => true,
                'is_wisdom' => false
            ],
            5 => [
                'title' => [
                    'en' => 'Physical Movement',
                    'vi' => 'Vận động thể chất',
                    'de' => 'Körperliche Bewegung',
                    'kr' => '신체 움직임'
                ],
                'description' => [
                    'en' => 'Engage in 20 minutes of physical activity you enjoy',
                    'vi' => 'Tham gia 20 phút hoạt động thể chất bạn thích',
                    'de' => 'Engagieren Sie sich in 20 Minuten körperlicher Aktivität, die Sie genießen',
                    'kr' => '즐기는 20분의 신체 활동에 참여하세요'
                ],
                'points' => 20,
                'is_body' => true,
                'is_mind' => false,
                'is_wisdom' => false
            ],
            6 => [
                'title' => [
                    'en' => 'Gratitude Moment',
                    'vi' => 'Khoảnh khắc biết ơn',
                    'de' => 'Dankbarkeitsmoment',
                    'kr' => '감사의 순간'
                ],
                'description' => [
                    'en' => 'Write down one thing you are grateful for despite your challenges',
                    'vi' => 'Viết ra một điều bạn biết ơn bất chấp những thách thức',
                    'de' => 'Schreiben Sie eine Sache auf, für die Sie trotz Ihrer Herausforderungen dankbar sind',
                    'kr' => '도전에도 불구하고 감사한 한 가지 적기'
                ],
                'points' => 10,
                'is_body' => false,
                'is_mind' => true,
                'is_wisdom' => false
            ],
            7 => [
                'title' => [
                    'en' => 'Learning Opportunity',
                    'vi' => 'Cơ hội học hỏi',
                    'de' => 'Lerngelegenheit',
                    'kr' => '학습 기회'
                ],
                'description' => [
                    'en' => 'Read or watch something educational about your challenge',
                    'vi' => 'Đọc hoặc xem nội dung giáo dục về thách thức của bạn',
                    'de' => 'Lesen oder schauen Sie etwas Bildungsfreies über Ihre Herausforderung',
                    'kr' => '도전에 대해 교육적인 것을 읽거나 보세요'
                ],
                'points' => 15,
                'is_body' => false,
                'is_mind' => false,
                'is_wisdom' => true
            ]
        ];

        return $missions[$painPointName] ?? $defaultMissions;
    }
}
