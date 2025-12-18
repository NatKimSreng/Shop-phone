<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Start query
        $query = Product::query()
            ->with('category')           // Eager load category
            ->where('stock', true);      // Only in-stock products

        // Filter by category (e.g. ?category=electronics)
        $selectedCategory = $request->category ?? null;
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search functionality (e.g. ?q=laptop)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }


        // Get latest products (12 for homepage grid)
        $products = $query->latest()->take(12)->get();

        // Get categories for dropdowns/filters
        $categories = Category::orderBy('category_name')->get();

        // Pass to view
        return view('home', compact('products', 'selectedCategory', 'categories'));
    }
    public function show(Product $product)
    {
        return view('frontend.products.show', data: compact(var_name: 'product'));
    }

}
