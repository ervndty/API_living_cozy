<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();

        // Filter by category
        if ($request->has('category_id')) {
            $products->where('category_id', $request->category_id);
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
        $limit = $request->query('limit', 10);
        $products = $products->paginate($limit);

        return response()->json($products);
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
}
