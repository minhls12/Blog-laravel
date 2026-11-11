<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Member\PostController as MemberPostController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// ---- Nhóm A: trang công khai ----
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// ---- Nhóm B: tài khoản ----
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Nhóm B: thành viên quản lý bài của mình ----
Route::middleware('auth')->prefix('my/posts')->name('member.posts.')->group(function () {
    Route::get('/', [MemberPostController::class, 'index'])->name('index');
    Route::get('/create', [MemberPostController::class, 'create'])->name('create');
    Route::post('/', [MemberPostController::class, 'store'])->name('store');
    Route::get('/trash', [MemberPostController::class, 'trash'])->name('trash');
    Route::get('/{post:id}/edit', [MemberPostController::class, 'edit'])->name('edit');
    Route::put('/{post:id}', [MemberPostController::class, 'update'])->name('update');
    Route::delete('/{post:id}', [MemberPostController::class, 'destroy'])->name('destroy');
    Route::patch('/{post:id}/restore', [MemberPostController::class, 'restore'])->name('restore')->withTrashed();
    Route::delete('/{post:id}/force', [MemberPostController::class, 'forceDelete'])->name('force')->withTrashed();
});

// ---- Nhóm C: bình luận ----
Route::middleware('auth')->group(function () {
    Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// ---- Nhóm D: khu vực quản trị ----
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::delete('/posts/{post:id}', [AdminPostController::class, 'destroy'])->name('posts.destroy')->withTrashed();
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
});
