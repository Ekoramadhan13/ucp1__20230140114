<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::withCount('products')->get();
        return view('category.index', compact('categories'));
    }

    public function create()
    {
        // Gate::authorize('manage-category');
        return view('category.create');
    }

    public function store(Request $request)
    {
        // Log untuk memantau data yang masuk
        \Illuminate\Support\Facades\Log::info('Mencoba menyimpan kategori:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|unique:category,name|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        $category = Category::create($validated);

        \Illuminate\Support\Facades\Log::info('Kategori berhasil disimpan:', $category->toArray());

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        // Gate::authorize('manage-category');
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // Gate::authorize('manage-category');

        $validated = $request->validate([
            'name' => 'required|string|unique:category,name,' . $category->id . '|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        $category->update($validated);

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Gate::authorize('manage-category');
        $category->delete();

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
