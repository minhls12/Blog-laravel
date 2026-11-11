<?php

use Illuminate\Support\Facades\Schedule;

// Mỗi phút kiểm tra và đăng các bài hẹn giờ đã đến hạn.
// Chạy khi phát triển: php artisan schedule:work
Schedule::command('posts:publish-scheduled')->everyMinute()->withoutOverlapping();
