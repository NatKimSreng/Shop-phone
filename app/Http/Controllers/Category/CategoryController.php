<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::orderBy('category_name')->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:200|unique:categories,category_name',
            'category_description' => 'nullable|string|max:65535',  // Text field, no strict max but safe limit
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified category.
     * This handles both admin and frontend views based on route.
     */
    public function show($slug)
    {
        // Try to find by ID first, then by category_name (for friendly URLs)
        $category = Category::where('id', $slug)
            ->orWhere('category_name', str_replace('-', ' ', $slug))
            ->firstOrFail();

        // Check if this is an admin route (would have admin prefix in URL)
        if (request()->is('admin/*')) {
            return view('categories.show', compact('category'));
        }

        // Frontend category page - show products
        $products = Product::where('category_id', $category->id)
            ->where('stock', 1)
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('category.show', compact('category', 'products'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|unique:categories,category_name,' . $category->id . '|max:200',
            'category_description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted!');
    }
}
