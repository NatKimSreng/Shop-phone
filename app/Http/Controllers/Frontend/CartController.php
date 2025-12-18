<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'qty' => 1,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Added to cart!');
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        return view('frontend.cart.index', compact('cart'));
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:999',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['qty'] = (int)$request->qty;
            session()->put('cart', $cart);
            
            if ($request->ajax()) {
                $subtotal = $cart[$id]['price'] * $cart[$id]['qty'];
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['qty'];
                }
                
                return response()->json([
                    'success' => true,
                    'subtotal' => number_format($subtotal, 2),
                    'total' => number_format($total, 2),
                    'item_count' => count($cart),
                ]);
            }
            
            return back()->with('success', 'Cart updated successfully!');
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        return back()->with('error', 'Item not found in cart.');
    }
}
