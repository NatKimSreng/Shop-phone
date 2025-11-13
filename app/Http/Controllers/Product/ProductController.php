<?php

namespace App\Http\Controllers\Product;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Please login first!');
        }

        $user = Auth::user();
        $categories = Category::orderBy('category_name')->get();  // Fetch all categories for dropdown

        $query = Product::query();  // Initialize query builder for Product

        // 🔍 Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter (using relationship)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Stock filter
        if ($request->filled('stock')) {
            $query->where('stock', $request->stock);
        }

        // Price filter (minimum price)
        if ($request->filled('price')) {
            $query->where('price', '<=', $request->price);
        }

        $products = $query->with('category')  // Eager-load to avoid N+1
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->appends($request->query());

        return view('products.index', compact('user', 'products', 'categories'));
    }


    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200|unique:products,name',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'boolean',
        ]);

        // Handle image upload (same style as logo upload)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $uploadPath = public_path('assets/upload/products');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);
            $validated['image'] = 'assets/upload/products/' . $imageName;
        }

        // Convert stock checkbox to boolean
        $validated['stock'] = $request->boolean('stock');

        // Create product record
        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        return view('products.create', compact('categories'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('category_name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Optional: delete old image
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // Generate unique file name
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move file to public/assets/upload/products
            $file->move(public_path('assets/upload/products'), $filename);

            // Save path in DB
            $product->image = 'assets/upload/products/' . $filename;
        }

        // Update other fields
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->has('stock') ? 1 : 0;

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }
}
