<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with('category')
            ->where('stock', 1);

        // Search functionality
        if (request()->filled('q')) {
            $search = request('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        $selectedCategory = request('category');
        if (request()->filled('category')) {
            $query->where('category_id', $selectedCategory);
        }

        $products = $query->orderBy('id', 'desc')->paginate(12);

        return view('frontend.products.index', compact('products', 'selectedCategory'));
    }

    public function show(Product $product)
    {
        return view('frontend.products.show', compact('product'));
    }
}

