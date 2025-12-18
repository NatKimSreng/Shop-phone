<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $ordersMap = [];
        
        foreach ($rows as $row) {
            try {
                // Get or generate order number
                $orderNumber = $row['order_number'] ?? null;
                
                // If order number exists in map, it means we're adding items to existing order
                if ($orderNumber && isset($ordersMap[$orderNumber])) {
                    $order = $ordersMap[$orderNumber];
                } else {
                    // Create new order
                    $orderData = [
                        'order_number' => $orderNumber ?: Order::generateOrderNumber(),
                        'name' => $row['customer_name'] ?? $row['name'] ?? null,
                        'email' => $row['email'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null,
                        'city' => $row['city'] ?? null,
                        'postal_code' => $row['postal_code'] ?? null,
                        'country' => $row['country'] ?? 'Cambodia',
                        'total' => 0, // Will be calculated from items
                        'subtotal' => $row['subtotal'] ?? null,
                        'status' => $row['status'] ?? 'pending',
                        'payment_method' => $row['payment_method'] ?? 'cod',
                        'notes' => $row['notes'] ?? null,
                    ];

                    // Validate required fields
                    if (empty($orderData['name']) || empty($orderData['email']) || empty($orderData['phone']) || empty($orderData['address'])) {
                        Log::warning('Skipping row with missing required fields', ['row' => $row->toArray()]);
                        continue;
                    }

                    $order = Order::create($orderData);
                    $ordersMap[$order->order_number] = $order;
                }

                // Add order item if product information is provided
                if (isset($row['product_id']) && isset($row['quantity'])) {
                    $productId = $row['product_id'];
                    $quantity = (int)($row['quantity'] ?? 1);
                    
                    // Check if product exists
                    $product = Product::find($productId);
                    if (!$product) {
                        Log::warning('Product not found', ['product_id' => $productId, 'order_id' => $order->id]);
                        continue;
                    }

                    // Get price from row or use product price
                    $price = $row['price'] ?? $product->price;
                    
                    // Check if order item already exists for this product
                    $existingItem = OrderItem::where('order_id', $order->id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($existingItem) {
                        // Update quantity and recalculate
                        $existingItem->qty += $quantity;
                        $existingItem->subtotal = $existingItem->price * $existingItem->qty;
                        $existingItem->save();
                    } else {
                        // Create new order item
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $productId,
                            'qty' => $quantity,
                            'price' => $price,
                            'subtotal' => $price * $quantity,
                        ]);
                    }

                    // Recalculate order total
                    $order->total = OrderItem::where('order_id', $order->id)->sum('subtotal');
                    if (!$order->subtotal) {
                        $order->subtotal = $order->total;
                    }
                    $order->save();
                } else {
                    // If no product info, just set total from row if provided
                    if (isset($row['total'])) {
                        $order->total = $row['total'];
                        if (!$order->subtotal) {
                            $order->subtotal = $order->total;
                        }
                        $order->save();
                    }
                }

            } catch (\Exception $e) {
                Log::error('Error importing order row', [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'total' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,paid,processing,shipped,delivered,cancelled',
            'payment_method' => 'nullable|in:cod,khqr',
            'product_id' => 'nullable|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ];
    }
}

