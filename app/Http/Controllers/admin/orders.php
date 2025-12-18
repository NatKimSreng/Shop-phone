<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Imports\OrderImport;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class orders extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product', 'user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

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

        $orders = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        // Orders are created through checkout, not admin panel
        return redirect()->route('admin.orders.index')
            ->with('info', 'Orders are created through the checkout process.');
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        // Orders are created through checkout, not admin panel
        return redirect()->route('admin.orders.index')
            ->with('info', 'Orders are created through the checkout process.');
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        // Redirect to show page - orders are edited via status update
        return redirect()->route('admin.orders.show', $order);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product.category', 'user']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
        ]);

        // If status is being set to paid, also set paid_at timestamp
        if ($validated['status'] === 'paid' && !$order->paid_at) {
            $validated['paid_at'] = now();
        }

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully!');
    }

    /**
     * Show import form or handle import.
     */
    public function import(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
            ]);

            try {
                Excel::import(new OrderImport, $request->file('file'));

                return redirect()->route('admin.orders.index')
                    ->with('success', 'Orders imported successfully!');
            } catch (\Exception $e) {
                Log::error('Order import failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()->route('admin.orders.index')
                    ->with('error', 'Import failed: ' . $e->getMessage());
            }
        }

        // If GET request, show import form (we'll add this to the index view)
        return redirect()->route('admin.orders.index');
    }

    /**
     * Download sample Excel template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'order_number',
            'customer_name',
            'email',
            'phone',
            'address',
            'city',
            'postal_code',
            'country',
            'total',
            'subtotal',
            'status',
            'payment_method',
            'notes',
            'product_id',
            'quantity',
            'price',
        ];

        $sampleData = [
            [
                'ORD-20250101-001',
                'John Doe',
                'john@example.com',
                '012345678',
                '123 Main Street',
                'Phnom Penh',
                '12000',
                'Cambodia',
                '50.00',
                '50.00',
                'pending',
                'cod',
                'Please deliver in the morning',
                '1',
                '2',
                '25.00',
            ],
        ];

        $export = new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromCollection {
            private $headers;
            private $data;

            public function __construct($headers, $data)
            {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function collection()
            {
                return collect([$this->headers])->merge($this->data);
            }
        };

        return Excel::download($export, 'orders_import_template.xlsx');
    }

    /**
     * Export orders to Excel.
     */
    public function export(Request $request)
    {
        try {
            $query = Order::with(['orderItems.product', 'user']);

            // Apply same filters as index method
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $orders = $query->latest()->get();

            if ($orders->isEmpty()) {
                return redirect()->route('admin.orders.index')
                    ->with('error', 'No orders found to export.');
            }

            $filename = 'orders_export_' . date('Y-m-d_His') . '.xlsx';

            return Excel::download(new OrderExport($orders), $filename);
        } catch (\Exception $e) {
            Log::error('Order export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.orders.index')
                ->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}
