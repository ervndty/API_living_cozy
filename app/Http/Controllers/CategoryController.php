<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategoryCount()
    {
        $count = Category::count();
        return response()->json(['jumlah_category' => $count]);
    }

    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:categories,nama_kategori',
        ]);

        $category = Category::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }

    public function getByName($name)
    {
        $category = Category::where('nama_kategori', $name)->first();

        if ($category) {
            return response()->json($category);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:categories,nama_kategori,' . $id . ',category_id',
        ]);

        $category = Category::findOrFail($id);
        $category->nama_kategori = $request->nama_kategori;
        $category->save();

        return response()->json($category);
    }
}

