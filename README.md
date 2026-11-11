# Blog cộng đồng (Đồ án cuối khóa Laravel)

- **Sinh viên:** Lương Văn Minh – MSV 6652151 – Lớp K66CNTTC
- **Công nghệ:** Laravel 13, PHP 8.3+, SQLite/MySQL, Blade, Bootstrap 5 (CDN)

Website blog nhiều tác giả: khách đọc và tìm bài, thành viên đăng bài và bình luận, quản trị viên quản lý nội dung toàn trang.

## Chức năng

- **Khách:** xem bài đã đăng (mới nhất trước, có phân trang), tìm kiếm, lọc theo danh mục và tag, đăng ký tài khoản.
- **Thành viên:** tạo/sửa bài có ảnh, danh mục, nhiều tag; 3 trạng thái nháp / đã đăng / hẹn giờ đăng; xóa vào thùng rác, khôi phục hoặc xóa vĩnh viễn; bình luận.
- **Quản trị viên (`/admin`):** thống kê, quản lý danh mục, xóa bài và bình luận vi phạm.
- **Xử lý nền:** tự động đăng bài hẹn giờ (Artisan Command + Task Scheduling); gửi email cho tác giả khi có bình luận mới (Queue).

## Cài đặt

Yêu cầu: PHP 8.3+ (bật các extension `gd`, `mbstring`, `openssl`, `pdo_sqlite`, `zip`), Composer.

```bash
git clone <địa-chỉ-repo>
cd <tên-thư-mục>
composer install
copy .env.example .env        # Linux/macOS: cp .env.example .env
php artisan key:generate
```

Mặc định dùng SQLite. Tạo file database rỗng rồi migrate:

```bash
type nul > database\database.sqlite    # Linux/macOS: touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Mở http://127.0.0.1:8000

Nếu dùng MySQL: tạo database trước, rồi sửa các dòng `DB_*` trong `.env`.

## Tài khoản mẫu (mật khẩu: `password`)

| Vai trò | Email |
|---|---|
| Quản trị viên | admin@example.com |
| Thành viên | member@example.com |

## Chạy xử lý nền (mở terminal riêng cho mỗi lệnh)

```bash
php artisan queue:work        # gửi email bình luận (MAIL_MAILER=log nên email ghi vào storage/logs/laravel.log)
php artisan schedule:work     # mỗi phút tự đăng các bài hẹn giờ đã đến hạn
php artisan posts:publish-scheduled   # hoặc chạy tay
```

## Chạy test

```bash
php artisan test
```

Gồm 38 Feature test và 4 Unit test, mỗi chức năng đều có trường hợp thành công và thất bại. Test upload ảnh cần extension `gd`.

## Đối chiếu yêu cầu đề bài

| Nhóm | Yêu cầu | Nằm ở đâu |
|---|---|---|
| A | Trang chủ, phân trang, mới nhất trước | `PostController@index`, `posts/index.blade.php` |
| A | Chi tiết bài theo slug | `routes/web.php` (`{post:slug}`), `posts/show.blade.php` |
| A | Tìm kiếm, lọc danh mục/tag | `Post::scopeSearch`, `PostController@index` |
| B | Đăng ký/đăng nhập/đăng xuất | `AuthController` |
| B | Tạo bài: ảnh, danh mục, nhiều tag | `Member\PostController`, `PostRequest` |
| B | 3 trạng thái nháp / đã đăng / hẹn giờ | `Post::STATUSES`, `PostRequest` |
| B | Chỉ tác giả được sửa (chặn ở server) | `PostPolicy`, `PostRequest::authorize()` |
| B | Thùng rác, khôi phục, xóa vĩnh viễn | `SoftDeletes`, `Member\PostController` |
| C | Bình luận; người viết / tác giả / admin được xóa | `CommentController`, `CommentPolicy` |
| D | `/admin` chỉ admin vào được | middleware `EnsureUserIsAdmin` |
| D | Thống kê, top 5 bài nhiều bình luận | `Admin\DashboardController` |
| D | CRUD danh mục, xóa bài/bình luận vi phạm | `Admin\CategoryController`, `Admin\PostController`, `Admin\CommentController` |
| E | Đăng bài hẹn giờ tự động | `PublishScheduledPosts`, `routes/console.php` |
| E | Email qua Queue khi có bình luận | `NewCommentMail`, `CommentController@store` |
| F | Migration, khóa ngoại, Seeder, Factory | `database/` |
| F | Trang lỗi 403, 404, 500 | `resources/views/errors/` |
| F | Ghi log sự kiện quan trọng | `Log::info/warning` trong controller và command |
| F | Feature test, Unit test | `tests/Feature`, `tests/Unit` |