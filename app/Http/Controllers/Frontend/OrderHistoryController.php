<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    /**
     * Display the authenticated user's order history.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to view your order history.');
        }

        $query = Auth::user()->orders()->with('orderItems.product');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10)->appends($request->query());

        return view('frontend.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to view order details.');
        }

        // Ensure user can only view their own orders (unless admin)
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('orders.index')
                ->with('error', 'You do not have permission to view this order.');
        }

        $order->load(['orderItems.product.category']);

        return view('frontend.orders.show', compact('order'));
    }
}

