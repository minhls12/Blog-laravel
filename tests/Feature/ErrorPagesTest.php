<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_404_page(): void
    {
        $this->get('/trang-khong-ton-tai')->assertNotFound()->assertSee('Không tìm thấy trang');
    }

    public function test_custom_403_page(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden()->assertSee('Không có quyền truy cập');
    }

    public function test_custom_500_page_renders(): void
    {
        $this->assertStringContainsString('Lỗi máy chủ', view('errors.500')->render());
    }
}
