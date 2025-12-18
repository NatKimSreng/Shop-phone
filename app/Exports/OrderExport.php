<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $orders;

    public function __construct($orders = null)
    {
        $this->orders = $orders ?? collect([]);
        
        // Ensure it's a collection
        if (!$this->orders instanceof \Illuminate\Support\Collection) {
            $this->orders = collect($this->orders);
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->orders->isEmpty() ? collect([]) : $this->orders;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Order Number',
            'Customer Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'Postal Code',
            'Country',
            'Total',
            'Subtotal',
            'Status',
            'Payment Method',
            'Notes',
            'Order Date',
            'Paid Date',
            'Items Count',
            'Products',
        ];
    }

    /**
     * @param Order $order
     * @return array
     */
    public function map($order): array
    {
        // Get product names - handle null/empty orderItems
        $orderItems = $order->orderItems ?? collect([]);
        $products = $orderItems->map(function ($item) {
            $productName = ($item->product && $item->product->name) 
                ? $item->product->name 
                : 'Product #' . ($item->product_id ?? 'N/A');
            return $productName . ' (Qty: ' . ($item->qty ?? 0) . ')';
        })->implode('; ');

        return [
            $order->order_number ?? 'ORD-' . $order->id,
            $order->name ?? '',
            $order->email ?? '',
            $order->phone ?? '',
            $order->address ?? '',
            $order->city ?? '',
            $order->postal_code ?? '',
            $order->country ?? 'Cambodia',
            $order->total ? number_format((float)$order->total, 2) : '0.00',
            $order->subtotal ? number_format((float)$order->subtotal, 2) : ($order->total ? number_format((float)$order->total, 2) : '0.00'),
            ucfirst($order->status ?? 'pending'),
            strtoupper($order->payment_method ?? 'cod'),
            $order->notes ?? '',
            $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
            ($order->paid_at && $order->paid_at instanceof \Carbon\Carbon) ? $order->paid_at->format('Y-m-d H:i:s') : '',
            $orderItems->count(),
            $products ?: 'No products',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 18,  // Order Number
            'B' => 20,  // Customer Name
            'C' => 25,  // Email
            'D' => 15,  // Phone
            'E' => 30,  // Address
            'F' => 15,  // City
            'G' => 12,  // Postal Code
            'H' => 15,  // Country
            'I' => 12,  // Total
            'J' => 12,  // Subtotal
            'K' => 12,  // Status
            'L' => 15,  // Payment Method
            'M' => 30,  // Notes
            'N' => 20,  // Order Date
            'O' => 20,  // Paid Date
            'P' => 12,  // Items Count
            'Q' => 40,  // Products
        ];
    }
}

