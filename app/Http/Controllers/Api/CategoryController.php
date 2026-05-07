<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'message' => 'Daftar kategori berhasil diambil',
                'data' => $categories
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil daftar kategori', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!\Illuminate\Support\Facades\Gate::allows('manage-category')) {
            return response()->json(['message' => 'Hanya Admin yang dapat menambah kategori'], 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:category,name'
            ]);

            $category = Category::create($validated);

            Log::info('Menambah kategori baru', ['category' => $category]);

            return response()->json([
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah kategori', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal menambah kategori', 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $category = Category::with('products')->find($id);

            if (!$category) {
                return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
            }

            return response()->json([
                'message' => 'Kategori berhasil ditemukan',
                'data' => $category
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat mengambil kategori', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        if (!\Illuminate\Support\Facades\Gate::allows('manage-category')) {
            return response()->json(['message' => 'Hanya Admin yang dapat mengubah kategori'], 403);
        }

        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:category,name,' . $id
            ]);

            $category->update($validated);

            Log::info('Memperbarui kategori', ['category' => $category]);

            return response()->json([
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat memperbarui kategori', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal memperbarui kategori', 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        if (!\Illuminate\Support\Facades\Gate::allows('manage-category')) {
            return response()->json(['message' => 'Hanya Admin yang dapat menghapus kategori'], 403);
        }

        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
            }

            // Opsional: Cek jika kategori memiliki produk sebelum dihapus
            if ($category->products()->count() > 0) {
                return response()->json(['message' => 'Kategori tidak bisa dihapus karena masih memiliki produk'], 400);
            }

            $category->delete();

            Log::info('Menghapus kategori', ['id' => $id]);

            return response()->json([
                'message' => 'Kategori berhasil dihapus'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat menghapus kategori', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal menghapus kategori'], 500);
        }
    }
}
