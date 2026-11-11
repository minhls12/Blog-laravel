<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Khi sửa: kiểm tra quyền ngay tại server, trước cả validate.
        $post = $this->route('post');

        return $post instanceof Post ? Gate::allows('update', $post) : true;
    }

    protected function prepareForValidation(): void
    {
        // Chỉ giữ giờ hẹn khi chọn trạng thái "hẹn giờ đăng".
        if ($this->input('status') !== Post::SCHEDULED) {
            $this->merge(['published_at' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'status' => ['required', Rule::in(array_keys(Post::STATUSES))],
            'published_at' => ['nullable', 'required_if:status,scheduled', 'date', 'after:now'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề',
            'body' => 'nội dung',
            'category_id' => 'danh mục',
            'tags' => 'tag',
            'status' => 'trạng thái',
            'published_at' => 'thời gian hẹn đăng',
            'image' => 'ảnh',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'required_if' => 'Vui lòng chọn :attribute.',
            'published_at.after' => 'Thời gian hẹn đăng phải ở tương lai.',
            'image.image' => 'File tải lên phải là ảnh.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',
            'body.min' => 'Nội dung quá ngắn (tối thiểu 10 ký tự).',
        ];
    }
}
