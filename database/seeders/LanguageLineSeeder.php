<?php

namespace Database\Seeders;

use App\Models\LanguageLine;
use Illuminate\Database\Seeder;

class LanguageLineSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'group' => 'ui',
                'key' => 'dashboard_title',
                'text' => [
                    'vi' => 'Bảng điều khiển',
                    'en' => 'Dashboard',
                    'de' => 'Armaturenbrett',
                    'kr' => '대시보드',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'start_mission',
                'text' => [
                    'vi' => 'Bắt đầu nhiệm vụ',
                    'en' => 'Start Mission',
                    'de' => 'Mission starten',
                    'kr' => '미션 시작',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'user_panel',
                'text' => [
                    'vi' => 'Bảng người dùng',
                    'en' => 'User Panel',
                    'de' => 'Benutzerbereich',
                    'kr' => '사용자 패널',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'dashboard',
                'text' => [
                    'vi' => 'Bảng điều khiển',
                    'en' => 'Dashboard',
                    'de' => 'Dashboard',
                    'kr' => '대시보드',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'soul_assessment',
                'text' => [
                    'vi' => 'Kiểm tra tâm hồn',
                    'en' => 'Soul Assessment',
                    'de' => 'Seelen-Test',
                    'kr' => '영혼 검사',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'videos',
                'text' => [
                    'vi' => 'Thư viện chữa lành',
                    'en' => 'Healing Library',
                    'de' => 'Heilungsbibliothek',
                    'kr' => '치유 라이브러리',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'my_tree',
                'text' => [
                    'vi' => 'Cây tâm hồn',
                    'en' => 'My Soul Tree',
                    'de' => 'Mein Seelenbaum',
                    'kr' => '나의 영혼 나무',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'consultations',
                'text' => [
                    'vi' => 'Góc tư vấn',
                    'en' => 'Consultation Corner',
                    'de' => 'Beratungsecke',
                    'kr' => '상담 코너',
                ],
            ],
            [
                'group' => 'menu',
                'key' => 'profile',
                'text' => [
                    'vi' => 'Hồ sơ',
                    'en' => 'Profile',
                    'de' => 'Profil',
                    'kr' => '프로필',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'save',
                'text' => [
                    'vi' => 'Lưu',
                    'en' => 'Save',
                    'de' => 'Speichern',
                    'kr' => '저장',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'cancel',
                'text' => [
                    'vi' => 'Hủy',
                    'en' => 'Cancel',
                    'de' => 'Abbrechen',
                    'kr' => '취소',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'email',
                'text' => [
                    'vi' => 'Email',
                    'en' => 'Email',
                    'de' => 'E-Mail',
                    'kr' => '이메일',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'password',
                'text' => [
                    'vi' => 'Mật khẩu',
                    'en' => 'Password',
                    'de' => 'Passwort',
                    'kr' => '비밀번호',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'hi_name',
                'text' => [
                    'vi' => 'Chào :name',
                    'en' => 'Hi :name',
                    'de' => 'Hallo :name',
                    'kr' => ':name님 안녕하세요',
                ],
            ],
            [
                'group' => 'ui',
                'key' => 'role_panel',
                'text' => [
                    'vi' => 'Bảng :role',
                    'en' => ':role Panel',
                    'de' => ':role-Bereich',
                    'kr' => ':role 패널',
                ],
            ],
            [
                'group' => 'auth',
                'key' => 'logout',
                'text' => [
                    'vi' => 'Đăng xuất',
                    'en' => 'Logout',
                    'de' => 'Abmelden',
                    'kr' => '로그아웃',
                ],
            ],
            [
                'group' => 'validation',
                'key' => 'please_fix_errors',
                'text' => [
                    'vi' => 'Vui lòng sửa các lỗi sau:',
                    'en' => 'Please fix the following errors:',
                    'de' => 'Bitte beheben Sie die folgenden Fehler:',
                    'kr' => '다음 오류를 수정해주세요:',
                ],
            ],
        ];

        foreach ($rows as $row) {
            $existing = LanguageLine::query()
                ->where('group', $row['group'])
                ->where('key', $row['key'])
                ->first();

            if ($existing) {
                $existing->update([
                    'text' => $row['text'],
                ]);
            } else {
                LanguageLine::create($row);
            }
        }
    }
}
