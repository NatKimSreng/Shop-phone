<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->where('stock', true);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $selectedCategory = $request->category ?? null;
        if ($request->filled('category')) {
            $query->where('category_id', $selectedCategory);
        }

        $products = $query->latest()->paginate(16);

        return view('search.results', [
            'products' => $products,
            'searchQuery' => $request->q,
            'selectedCategory' => $selectedCategory
        ]);
    }
}
