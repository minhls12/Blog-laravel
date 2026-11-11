<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.categories.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $category = Category::create($this->validated($request));
        Log::info('Admin thêm danh mục', ['category_id' => $category->id, 'admin_id' => $request->user()->id]);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));
        Log::info('Admin sửa danh mục', ['category_id' => $category->id, 'admin_id' => $request->user()->id]);

        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $category->delete(); // bài viết thuộc danh mục này được đặt category_id = null
        Log::warning('Admin xóa danh mục', ['category_id' => $category->id, 'admin_id' => $request->user()->id]);

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $request->merge(['slug' => Str::slug((string) $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category)],
            'slug' => [Rule::unique('categories', 'slug')->ignore($category)],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'slug.unique' => 'Tên danh mục bị trùng với danh mục khác.',
        ]);
    }
}
