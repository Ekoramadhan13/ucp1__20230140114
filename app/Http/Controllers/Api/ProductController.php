<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::with('category', 'user')->get();
            return response()->json([
                'message' => 'Daftar produk berhasil diambil',
                'data' => $products
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil daftar produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Map 'quantity' from request to 'qty' in database
            if (isset($validated['quantity'])) {
                $validated['qty'] = $validated['quantity'];
            }
            
            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            Log::info('Menambah data produk', [
                'list' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan!!',
                'data' => $product,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal menambah produk'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $product = Product::with('category', 'user')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Produk tidak ditemukan'], 404);
            }

            // Otorisasi: Hanya pemilik atau admin yang bisa edit
            if ($product->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                return response()->json(['message' => 'Forbidden: Anda bukan pemilik produk ini'], 403);
            }

            $validated = $request->validated();
            if (isset($validated['quantity'])) {
                $validated['qty'] = $validated['quantity'];
            }

            $product->update($validated);

            Log::info('Memperbarui data produk', ['product' => $product]);

            return response()->json([
                'message' => 'Produk berhasil diperbarui',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat memperbarui produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal memperbarui produk'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Produk tidak ditemukan'], 404);
            }

            // Otorisasi: Hanya pemilik atau admin yang bisa hapus
            if ($product->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                return response()->json(['message' => 'Forbidden: Anda tidak diizinkan menghapus produk ini'], 403);
            }

            $product->delete();

            Log::info('Menghapus data produk', ['id' => $id]);

            return response()->json([
                'message' => 'Produk berhasil dihapus'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat menghapus produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal menghapus produk'], 500);
        }
    }
}
