<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DailyTask;
use Illuminate\Support\Facades\DB;

class DailyTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            // --- PILLAR: HEART (TRÁI TIM - Yêu thương & Tử tế) ---
            ['content' => 'Nhắn tin cảm ơn một người bạn cũ vì điều họ đã làm trong quá khứ.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Gọi điện cho bố hoặc mẹ (hoặc người thân) chỉ để hỏi thăm sức khỏe, không nhờ vả gì.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Mỉm cười thật tươi với một người lạ hoặc bác bảo vệ/lao công.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Viết ra 3 điều bạn biết ơn nhất trong ngày hôm nay.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Gửi một lời khen ngợi chân thành đến đồng nghiệp hoặc bạn bè.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Thực hành "Bố thí nụ cười": Không cau có khi gặp chuyện không như ý.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Nhặt rác xung quanh nơi bạn đứng (dù không phải rác của bạn).', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Tha thứ cho một lỗi lầm nhỏ của ai đó trong ngày hôm nay.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Để dành một phần đồ ăn/nước uống mời người khác.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Lắng nghe trọn vẹn câu chuyện của ai đó mà không ngắt lời hay phán xét.', 'tag' => 'heart', 'diff' => 'hard'],
            ['content' => 'Viết một lá thư (không cần gửi) cho người làm bạn tổn thương để buông bỏ nỗi đau.', 'tag' => 'heart', 'diff' => 'hard'],
            ['content' => 'Quyên góp một số tiền nhỏ (dù chỉ 5k-10k) vào quỹ từ thiện bất kỳ.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Tưới cây hoặc chăm sóc thú cưng với sự chú tâm trọn vẹn.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Nói lời "Xin lỗi" chân thành nếu bạn lỡ làm phiền ai đó.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Tìm điểm tốt của người mà bạn ghét nhất.', 'tag' => 'heart', 'diff' => 'hard'],
            ['content' => 'Ôm người thân thật chặt trong ít nhất 10 giây.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Không phàn nàn về bất cứ điều gì trong suốt 15 phút tới.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Dành lời chúc thầm kín: "Mong người này được bình an" khi nhìn thấy ai đó.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Sắp xếp lại dép/giày cho gọn gàng ở nơi công cộng.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Viết nhật ký về một kỷ niệm đẹp khiến bạn cảm động.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Tự thưởng cho bản thân một lời động viên: "Mình đã làm tốt rồi".', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Giúp đỡ ai đó một việc nhỏ mà không mong cầu sự công nhận.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Suy ngẫm về sự hy sinh của cha mẹ/tổ tiên để bạn có mặt hôm nay.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Gửi tin nhắn chúc buổi sáng tốt lành đến 3 người bạn.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Dọn dẹp bàn ăn sau khi ăn xong (ở quán hoặc ở nhà).', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Thực tập hạnh kiên nhẫn: Không bấm còi khi kẹt xe/đèn đỏ.', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Dành 15 phút chơi trọn vẹn với con cái hoặc thú cưng (không điện thoại).', 'tag' => 'heart', 'diff' => 'medium'],
            ['content' => 'Cảm ơn cơ thể của mình vì vẫn đang hoạt động khỏe mạnh.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Chia sẻ kiến thức hay bạn vừa học được cho người khác.', 'tag' => 'heart', 'diff' => 'easy'],
            ['content' => 'Nguyện cầu cho sự bình an của thế giới (hoặc một nơi đang có chiến tranh/thiên tai).', 'tag' => 'heart', 'diff' => 'easy'],

            // --- PILLAR: GRIT (NỘI LỰC - Tĩnh lặng & Bền bỉ) ---
            ['content' => 'Ngồi yên tĩnh, chỉ quan sát hơi thở vào-ra trong 5 phút.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Tắt điện thoại và wifi hoàn toàn trong 15 phút.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Ăn một bữa ăn trong chánh niệm (không TV, không điện thoại, nhai kỹ).', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Đi bộ 100 bước thật chậm, cảm nhận bàn chân chạm đất.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Tắm nước lạnh (hoặc rửa mặt nước lạnh) để đánh thức giác quan.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Giữ lưng thẳng tắp trong suốt thời gian làm việc 15 phút.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Khi có tin nhắn đến, đợi 3 hơi thở rồi mới cầm điện thoại lên.', 'tag' => 'grit', 'diff' => 'hard'],
            ['content' => 'Tập trung làm duy nhất một việc trong 15 phút (Single-tasking).', 'tag' => 'grit', 'diff' => 'hard'],
            ['content' => 'Quan sát một cơn ngứa hoặc đau nhẹ trên cơ thể mà không gãi/đổi tư thế ngay.', 'tag' => 'grit', 'diff' => 'hard'],
            ['content' => 'Uống một ly nước thật chậm, cảm nhận dòng nước trôi vào họng.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Dọn dẹp giường ngủ ngay sau khi thức dậy.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Ngồi thiền Body Scan (quét cơ thể) từ đầu đến chân trong 10 phút.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Không nói chuyện (Tịnh khẩu) trong 15 phút để quan sát tâm.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Nhìn ngắm bầu trời hoặc một cái cây trong 5 phút mà không suy nghĩ gì.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Thực hiện 3 động tác hít đất hoặc squat để kích hoạt năng lượng.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Khi cơn giận/khó chịu nổi lên, hãy niệm thầm: "Cảm xúc này đang ghé thăm".', 'tag' => 'grit', 'diff' => 'hard'],
            ['content' => 'Đi ngủ sớm hơn 15 phút so với mọi ngày.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Thức dậy sớm hơn 15 phút và dành thời gian đó để ngồi yên.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Thực hành 4-7-8: Hít vào 4s, giữ 7s, thở ra 8s (3 lần).', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Rửa bát đĩa với sự chú tâm hoàn toàn vào cảm giác tay chạm nước.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Nghe một bản nhạc không lời và theo dõi từng nốt nhạc.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Tắt thông báo (Notification) các app không cần thiết.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Chịu đựng cảm giác đói thêm 15 phút trước khi ăn (nếu sức khỏe cho phép).', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Đi chân trần trên cỏ hoặc sàn đất/đá.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Đọc một trang sách giấy mà không để tâm trí bay đi đâu.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Giữ im lặng tuyệt đối khi đang ăn.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Khi đi vệ sinh, không mang theo điện thoại.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Quan sát hơi thở bụng phồng lên xẹp xuống 10 lần trước khi ngủ.', 'tag' => 'grit', 'diff' => 'easy'],
            ['content' => 'Chỉ mua những thứ thực sự cần thiết trong ngày hôm nay.', 'tag' => 'grit', 'diff' => 'medium'],
            ['content' => 'Cam kết hoàn thành một việc nhỏ đã trì hoãn từ lâu.', 'tag' => 'grit', 'diff' => 'hard'],

            // --- PILLAR: WISDOM (TRÍ TUỆ - Hiểu biết & Sự thật) ---
            ['content' => 'Viết ra một thất bại gần đây và bài học rút ra từ nó.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Đặt câu hỏi "Tại sao?" 3 lần cho một vấn đề bạn đang gặp phải.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Đọc một bài viết hoặc xem video về chủ đề khoa học/vũ trụ.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Quán chiếu về cái chết: "Nếu hôm nay là ngày cuối, mình sẽ làm gì?".', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Quan sát một bông hoa héo và ngẫm về sự Vô thường (thay đổi).', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Nhận diện một "định kiến" mà bạn đang áp đặt lên người khác.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Viết ra 3 nguyên nhân dẫn đến nỗi khổ hiện tại của bạn.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Tìm hiểu ý nghĩa của một câu tục ngữ hoặc danh ngôn.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Đặt mình vào vị trí người khác để hiểu tại sao họ hành động như vậy.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Xem lại chi tiêu trong ngày và phân biệt đâu là "Cần" và đâu là "Muốn".', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Nhận diện một thói quen xấu và phân tích nguyên nhân kích hoạt nó.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Đọc tiểu sử của một vĩ nhân để học hỏi tư duy của họ.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Tự hỏi: "Điều này có quan trọng trong 5 năm nữa không?" trước khi lo lắng.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Quan sát tâm trí mình xem đang nghĩ về Quá khứ, Hiện tại hay Tương lai.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Học một từ mới (tiếng Anh hoặc chuyên ngành) và dùng nó.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Phân tích chuỗi Nhân - Quả của một sự kiện vui vẻ hôm nay.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Chấp nhận một sự thật phũ phàng mà bạn đang trốn tránh.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Viết ra mục đích sống quan trọng nhất của bạn lúc này.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Thử làm một việc theo cách hoàn toàn mới (đi đường khác, ăn quán khác).', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Ngẫm về sự may mắn của mình so với những người khó khăn hơn.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Lên kế hoạch cụ thể cho ngày mai vào tối nay.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Tự hỏi: "Mình đang tìm kiếm hạnh phúc ở bên ngoài hay bên trong?".', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Đọc 5 trang sách về phát triển bản thân hoặc tâm linh.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Quan sát cảm xúc của mình như một người xem phim, không đồng nhất với nó.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Rút kinh nghiệm từ một lần nóng giận trong quá khứ.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Tìm hiểu về cách hoạt động của bộ não khi bị stress.', 'tag' => 'wisdom', 'diff' => 'easy'],
            ['content' => 'Viết ra những gì bạn kiểm soát được và không kiểm soát được.', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Thực hành tư duy phản biện: Tìm một lý lẽ phản bác lại niềm tin của chính mình.', 'tag' => 'wisdom', 'diff' => 'hard'],
            ['content' => 'Ngẫm về sự kết nối tương hỗ: Bữa cơm này đến từ công sức của bao nhiêu người?', 'tag' => 'wisdom', 'diff' => 'medium'],
            ['content' => 'Tổng kết tuần: Mình đã trưởng thành hơn ở điểm nào?', 'tag' => 'wisdom', 'diff' => 'medium'],
        ];

        $difficultyMap = [
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
        ];

        $typeMap = [
            'heart' => 'emotional',
            'grit' => 'mindfulness',
            'wisdom' => 'mindfulness',
        ];

        // Make seeding idempotent - use firstOrCreate to prevent duplicates
        foreach ($tasks as $index => $task) {
            DailyTask::firstOrCreate(
                ['day_number' => $index + 1],
                [
                    'content' => [
                        'vi' => $task['content'],
                        'en' => $task['content'], // Fallback
                        'kr' => $task['content'], // Fallback
                        'de' => $task['content'], // Fallback
                    ],
                    'pillar_tag' => $task['tag'],
                    'difficulty' => $task['diff'],
                    'difficulty_level_int' => $difficultyMap[$task['diff']] ?? 1,
                    'title' => [
                        'en' => 'Day ' . ($index + 1),
                        'vi' => 'Ngày ' . ($index + 1),
                        'kr' => 'Day ' . ($index + 1), // Placeholder
                        'de' => 'Tag ' . ($index + 1),
                    ],
                    'description' => [
                        'vi' => $task['content'],
                        'en' => $task['content'], // Placeholder as we don't have full translations
                        'kr' => $task['content'],
                        'de' => $task['content'],
                    ],
                    'type' => $typeMap[$task['tag']] ?? 'mindfulness',
                    'estimated_minutes' => 10,
                    'solution_id' => null,
                    'instructions' => [
                        'vi' => [
                            'content' => [$task['content']],
                            'pillar_tag' => $task['tag'],
                            'difficulty' => $task['diff'],
                        ],
                        'en' => [
                            'content' => [$task['content']],
                        ],
                        'kr' => [
                            'content' => [$task['content']],
                        ],
                        'de' => [
                            'content' => [$task['content']],
                        ],
                    ],
                    'status' => 'active',
                    'completed_at' => null,
                ]
            );
        }
    }
}
