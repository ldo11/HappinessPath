<?php

namespace Database\Seeders;

use App\Models\PainPoint;
use Illuminate\Database\Seeder;

class PainPointSeeder extends Seeder
{
    public function run(): void
    {
        $painPoints = [
            [
                'name' => 'Nóng giận mất kiểm soát',
                'category' => 'mind',
                'icon' => 'fire',
                'description' => 'Thường xuyên nổi điên, quát tháo, làm tổn thương người khác rồi hối hận.',
            ],
            [
                'name' => 'Mất kết nối với con cái',
                'category' => 'mind',
                'icon' => 'user-minus',
                'description' => 'Không nói chuyện được với con, con bướng bỉnh, xa cách.',
            ],
            [
                'name' => 'Hôn nhân rạn nứt',
                'category' => 'mind',
                'icon' => 'heart-crack',
                'description' => 'Vợ chồng khắc khẩu, lạnh nhạt hoặc đang đứng trước bờ vực ly hôn.',
            ],
            [
                'name' => 'Cô đơn, Trống rỗng',
                'category' => 'mind',
                'icon' => 'ghost',
                'description' => 'Cảm thấy lạc lõng giữa đám đông, không ai hiểu mình.',
            ],
            [
                'name' => 'Ghen tuông, Đố kỵ',
                'category' => 'mind',
                'icon' => 'eye',
                'description' => 'Khó chịu khi thấy người khác thành công hay hạnh phúc hơn mình.',
            ],
            [
                'name' => 'Tổn thương quá khứ',
                'category' => 'mind',
                'icon' => 'scar',
                'description' => 'Ám ảnh bởi những nỗi đau cũ, chưa thể tha thứ hay buông bỏ.',
            ],
            [
                'name' => 'Mất định hướng cuộc đời',
                'category' => 'wisdom',
                'icon' => 'compass-off',
                'description' => 'Không biết mình sinh ra để làm gì, sống vật vờ qua ngày.',
            ],
            [
                'name' => 'Chán nản công việc',
                'category' => 'wisdom',
                'icon' => 'briefcase-off',
                'description' => 'Đi làm như cực hình, muốn bỏ việc nhưng sợ thất nghiệp.',
            ],
            [
                'name' => 'Áp lực Nợ nần/Tài chính',
                'category' => 'wisdom',
                'icon' => 'money-bill-wave',
                'description' => 'Stress nặng vì tiền bạc, nợ nần do đầu tư sai hoặc chi tiêu quá đà.',
            ],
            [
                'name' => 'Sợ thất bại, Thiếu tự tin',
                'category' => 'wisdom',
                'icon' => 'shield-off',
                'description' => 'Muốn làm nhiều thứ nhưng nỗi sợ cản trở, không dám bước ra vùng an toàn.',
            ],
            [
                'name' => 'Nghiện mê tín dị đoan',
                'category' => 'wisdom',
                'icon' => 'crystal-ball',
                'description' => 'Phụ thuộc vào bói toán, cúng bái để giải quyết vấn đề thay vì nỗ lực.',
            ],
            [
                'name' => 'Mất ngủ triền miên',
                'category' => 'body',
                'icon' => 'moon',
                'description' => 'Khó ngủ, ngủ không sâu, thức dậy mệt mỏi lờ đờ.',
            ],
            [
                'name' => 'Nghiện Mạng xã hội/Game',
                'category' => 'body',
                'icon' => 'smartphone',
                'description' => 'Lướt điện thoại vô thức hàng giờ, lãng phí thời gian, mỏi mắt đau lưng.',
            ],
            [
                'name' => 'Trì hoãn, Lười biếng',
                'category' => 'body',
                'icon' => 'clock',
                'description' => 'Nước đến chân mới nhảy, việc hôm nay cứ để ngày mai.',
            ],
            [
                'name' => 'Stress, Căng thẳng tột độ',
                'category' => 'body',
                'icon' => 'brain',
                'description' => 'Đầu óc căng như dây đàn, hay đau đầu, tim đập nhanh.',
            ],
            [
                'name' => 'Sức khỏe suy kiệt',
                'category' => 'body',
                'icon' => 'battery-quarter',
                'description' => 'Thường xuyên ốm vặt, uể oải, thiếu năng lượng sống.',
            ],
            [
                'name' => 'Career',
                'category' => 'wisdom',
                'icon' => null,
                'description' => 'Career and work-related challenges.',
            ],
            [
                'name' => 'Love',
                'category' => 'mind',
                'icon' => null,
                'description' => 'Relationship and love-related challenges.',
            ],
            [
                'name' => 'Family',
                'category' => 'mind',
                'icon' => null,
                'description' => 'Family and parenting-related challenges.',
            ],
        ];

        foreach ($painPoints as $painPoint) {
            PainPoint::updateOrCreate(
                ['name' => $painPoint['name']],
                [
                    'category' => $painPoint['category'],
                    'icon' => $painPoint['icon'],
                    'description' => $painPoint['description'],
                ]
            );
        }
    }
}
