<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProductCount()
    {
        $count = Product::count();
        return response()->json(['jumlah_product' => $count]);
    }

    public function index(Request $request)
    {
        $products = Product::query();
        if ($request->has('category')) {
            $category = $request->category;
            $categoryId = \App\Models\Category::where('nama_kategori', $category)->value('category_id');
            if ($categoryId) {
                $products->where('category_id', $categoryId);
            }
        }
       

        // Search by name
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $products->where('product_name', 'like', '%' . $searchTerm . '%');
        }

        // Filter by price
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $validator = Validator::make($request->all(), [
                'min_price' => 'numeric',
                'max_price' => 'numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Min price and max price must be numeric.'], 400);
            }

            $products->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        // Sort by price
        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $sortOrder = $request->sort;
            $products->orderBy('price', $sortOrder);
        }

        // Paginate the results
        $limit = $request->query('limit', 8);
        $products = $products->paginate($limit);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        // Remove backslashes from image_url using regex
        if ($request->has('image_url')) {
            $cleanedImageUrl = preg_replace('/\\\\/', '', $request->image_url);
            $request->merge(['image_url' => $cleanedImageUrl]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,category_id',
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'image_url' => 'required|url',
            // tambahkan validasi lainnya sesuai kebutuhan
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Membuat produk baru
        $product = Product::create($request->all());
        return response()->json(['product' => $product], 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        return response()->json($product);
    }

    public function getByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)->get();

        if ($products->isEmpty()) {
            return response()->json(['error' => 'No products found for this category.'], 404);
        }

        return response()->json($products);
    }

    public function getAllCategories()
    {
        $categories = Product::pluck('category_id')->unique();
        return response()->json($categories);
    }

    public function update(Request $request, $id)
    {
        // Remove backslashes from image_url using regex
        if ($request->has('image_url')) {
            $cleanedImageUrl = preg_replace('/\\\\/', '', $request->image_url);
            $request->merge(['image_url' => $cleanedImageUrl]);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:categories,category_id',
            'product_name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'image_url' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find product by ID
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Update product
        $product->update($request->all());
        return response()->json(['product' => $product], 200);
    }


    public function destroy($id)
    {
        // Temukan produk berdasarkan ID
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Hapus produk
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }
}

