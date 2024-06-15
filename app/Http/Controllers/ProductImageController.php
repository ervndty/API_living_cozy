<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    public function index($productId)
    {
        $productImages = ProductImage::where('product_id', $productId)->get();

        if ($productImages->isEmpty()) {
            return response()->json(['error' => 'No product images found for this product.'], 404);
        }

        return response()->json($productImages);
    }

    public function store(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'image_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $productImage = new ProductImage();
        $productImage->product_id = $productId;
        $productImage->image_url = $request->input('image_url');
        $productImage->save();

        return response()->json($productImage, 201);
    }

    public function destroy($productId, $imageId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $productImage = ProductImage::where('product_id', $productId)->find($imageId);
        if (!$productImage) {
            return response()->json(['error' => 'Product image not found.'], 404);
        }

        $productImage->delete();

        return response()->json(['message' => 'Product image deleted successfully.']);
    }
}
