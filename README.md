# Con Đường Hạnh Phúc - Nền tảng Sức khỏe Tâm thần

Một nền tảng sức khỏe tâm thần toàn diện được xây dựng bằng Laravel, kết hợp thiền định có hướng dẫn, theo dõi phát triển cá nhân và hỗ trợ cộng đồng để giúp người dùng trên hành trình đến với sự bình yên nội tâm và sự minh triết.

## 🌟 Tính năng

### **Trải nghiệm Người dùng**
- **Đánh giá Cá nhân**: 30 câu hỏi đánh giá trên các trụ cột Tim, Ý chí và Trí tuệ
- **Thiền định PWA**: Bộ hẹn giờ thiền định toàn màn hình với các buổi hướng dẫn âm thanh
- **Nhiệm vụ Hàng ngày**: Hoạt động chăm sóc sức khỏe được cá nhân hóa dựa trên kết quả đánh giá
- **Hệ thống Tăng trưởng Cây**: Trực quan hóa hành trình sức khỏe tâm thần
- **Tính năng Cộng đồng**: Chia sẻ năng lượng tích cực với người dùng gần đó

### **Quản trị viên**
- **Quản lý Người dùng**: Hoạt động CRUD hoàn chỉnh cho tất cả loại người dùng
- **Quản lý Ngôn ngữ**: Hỗ trợ đa ngôn ngữ với hệ thống dịch thuật
- **Quản lý Nội dung**: Quản lý giải pháp và tài nguyên
- **Bảng điều khiển Phân tích**: Theo dõi hành trình người dùng và thống kê

### **Cổng Tình nguyện viên**
- **Xem xét Dịch thuật**: Xem xét và phê duyệt các bản dịch được tạo tự động
- **Theo dõi Tác động**: Phần thưởng EXP và thống kê đóng góp
- **Hỗ trợ Cộng đồng**: Giúp người dùng thông qua dịch thuật và kiểm duyệt nội dung

## 🚀 Bắt đầu Nhanh

### **Yêu cầu**
- PHP 8.1+
- Composer
- SQLite (mặc định) hoặc MySQL/PostgreSQL
- Node.js (cho tài nguyên frontend)

### **Cài đặt**

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd HappinessPath
   ```

2. **Cài đặt dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Thiết lập môi trường**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Thiết lập database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Khởi động server phát triển**
   ```bash
   php artisan serve
   ```

6. **Truy cập ứng dụng**
   - Mở trình duyệt đến `http://127.0.0.1:8001`
   - Sử dụng các nút đăng nhập kiểm tra trên trang chủ

## 🔑 Tài khoản Kiểm tra

Ứng dụng đi kèm với các tài khoản kiểm tra được cấu hình trước:

| Vai trò | Email | Mật khẩu | Quyền truy cập |
|--------|-------|----------|----------------|
| Admin | admin@happiness.test | 123456 | Bảng điều khiển admin đầy đủ |
| Người dùng | user@happiness.test | 123456 | Dashboard và thiền định |
| Tình nguyện viên | volunteer@happiness.test | 123456 | Xem xét dịch thuật |

## 📱 Tính năng PWA Di động

Nền tảng bao gồm các khả năng Progressive Web App:

- **Hỗ trợ Offline**: Hoạt động mà không cần kết nối internet
- **Có thể Cài đặt**: Có thể cài đặt như ứng dụng gốc
- **Ưu tiên Di động**: Tối ưu hóa cho thiết bị di động
- **Thông báo Đẩy**: Lời nhắc thiền định và cập nhật
- **Service Worker**: Bộ nhớ đệm và đồng bộ hóa nền

## 🧘 Tính năng Thiền định

### **Loại Thiền định**
- Chánh niệm (5, 10, 15 phút)
- Bài tập Hơi thở (5, 10, 15 phút)
- Lòng Từ Bi (5, 10, 15 phút)
- Quét Cơ thể (10, 20 phút)
- Thiền đi bộ (15 phút)

### **Quản lý Buổi học**
- Bộ hẹn giờ với vòng tiến trình trực quan
- Điều khiển phát lại âm thanh
- Ghi nhật ký buổi học và phần thưởng EXP
- Hỗ trợ thiền định offline

## 🌳 Hệ thống Hành trình Người dùng

### **Trụ cột Đánh giá**
- **Tim**: Trí tuệ cảm xúc và các mối quan hệ
- **Ý chí**: Sự kiên cường và tự kỷ luật
- **Trí tuệ**: Chánh niệm và tự nhận thức

### **Theo dõi Tăng trưởng**
- Hệ thống điểm kinh nghiệm (EXP)
- Trực quan hóa sức khỏe cây
- Tiến trình cấp độ
- Theo dõi chuỗi
- Cơ chế chia sẻ quả

## 🛠️ Phát triển

### **Cấu trúc Dự án**
```
├── app/
│   ├── Http/Controllers/
│   │   ├── Web/          # Controllers frontend
│   │   ├── Admin/        # Controllers bảng điều khiển admin
│   │   └── Volunteer/    # Controllers cổng tình nguyện viên
│   ├── Models/           # Models Eloquent
│   ├── Services/         # Services logic nghiệp vụ
│   └── Jobs/            # Background jobs
├── resources/views/
│   ├── layouts/         # Layouts Blade
│   ├── onboarding/      # Flow onboarding người dùng
│   ├── dashboard/       # Dashboard người dùng
│   ├── admin/          # Views bảng điều khiển admin
│   └── volunteer/      # Views cổng tình nguyện viên
└── database/
    ├── migrations/      # Migrations database
    └── seeders/        # Seeders database
```

### **Services Chính**
- **TreeService**: Xử lý buổi học thiền định và phần thưởng
- **GeminiTranslationService**: Dịch thuật nội dung hỗ trợ bởi AI
- **AutoTranslateJob**: Xử lý dịch thuật nền

### **Bảng Database**
- Users với quyền truy cập dựa trên vai trò (admin, member, volunteer)
- Câu hỏi và câu trả lời đánh giá
- Cây người dùng và theo dõi hành trình
- Nhiệm vụ hàng ngày và buổi học thiền định
- Nội dung đa ngôn ngữ và bản dịch

## 🔧 Cấu hình

### **Biến Môi trường**
```env
# App Configuration
APP_NAME="Con Đường Hạnh Phúc"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8001

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Translation Service
GEMINI_API_KEY=your_gemini_api_key_here
```

### **Cấu hình Cache**
```bash
# Tạo thư mục cache
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views

# Thiết lập quyền
chmod -R 755 storage bootstrap/cache

# Xóa cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## 🌐 Routes

### **Frontend Người dùng**
- `/` - Trang chủ với đăng nhập kiểm tra
- `/onboarding/step1` - Đăng ký
- `/onboarding/step2` - Quiz đánh giá
- `/onboarding/step3` - Kết quả
- `/dashboard` - Dashboard người dùng
- `/meditate` - PWA Thiền định

### **Bảng điều khiển Admin**
- `/admin/dashboard` - Dashboard admin
- `/admin/users` - Quản lý người dùng
- `/admin/languages` - Quản lý ngôn ngữ
- `/admin/solutions` - Quản lý nội dung

### **Cổng Tình nguyện viên**
- `/volunteer/dashboard` - Dashboard tình nguyện viên
- `/volunteer/translations` - Xem xét dịch thuật

## 🎨 Công nghệ Frontend

- **TailwindCSS**: Framework CSS utility-first
- **FontAwesome**: Thư viện icon
- **Blade Templates**: Engine templating của Laravel
- **Progressive Web App**: Service worker và manifest
- **Thiết kế Ưu tiên Di động**: Responsive và thân thiện với cảm ứng

## 📊 Phân tích & Theo dõi

### **Theo dõi Hành trình Người dùng**
- Tỷ lệ hoàn thành đánh giá
- Tần suất buổi học thiền định
- Hoàn thành nhiệm vụ hàng ngày
- Tiến trình tăng trưởng cây
- Số liệu tương tác cộng đồng

### **Thống kê Dashboard Admin**
- Tổng người dùng và phiên hoạt động
- Hàng đợi xem xét dịch thuật
- Thống kê quản lý nội dung
- Số liệu hiệu suất hệ thống

## 🔒 Tính năng Bảo mật

- Kiểm soát truy cập dựa trên vai trò
- Xác minh email
- Bảo vệ CSRF
- Xác thực và làm sạch đầu vào
- Hệ thống xác thực bảo mật

## 🌍 Hỗ trợ Đa ngôn ngữ

- Tiếng Việt (chính)
- Tiếng Anh (dịch tự động)
- Tiếng Đức (dịch tự động)
- Hệ thống ngôn ngữ mở rộng
- Xem xét dịch thuật tình nguyện viên

## 📈 Tối ưu hóa Hiệu suất

- Indexing database
- Tối ưu hóa truy vấn
- Chiến lược caching
- Tối thiểu hóa tài sản
- Lazy loading

## 🤝 Đóng góp

1. Fork repository
2. Tạo branch tính năng
3. Thực hiện thay đổi
4. Thêm kiểm tra nếu áp dụng
5. Gửi pull request

## 📄 Giấy phép

Dự án này được cấp phép theo Giấy phép MIT.

## 🆘 Hỗ trợ

Để được hỗ trợ và câu hỏi:
- Kiểm tra tài liệu
- Xem lại tài khoản kiểm tra
- Kiểm tra cấu trúc codebase
- Sử dụng bảng điều khiển admin để kiểm tra

---

**Xây dựng với lòng trắc ẩn và sự quan tâm đến sức khỏe tâm thần** 🌱
