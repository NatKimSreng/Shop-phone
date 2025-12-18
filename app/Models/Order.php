<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'total',
        'status',
        // Optional fields (will be added via migration)
        'order_number',
        'subtotal',
        'city',
        'postal_code',
        'country',
        'notes',
        'payment_method',
        'khqr_md5',
        'paid_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        try {
            $maxAttempts = 10;
            $attempt = 0;
            
            do {
                // Use microtime and random string for better uniqueness
                $number = 'ORD-' . date('YmdHis') . '-' . strtoupper(substr(uniqid('', true), -8));
                $attempt++;
                
                if ($attempt >= $maxAttempts) {
                    // Fallback: use timestamp with microseconds
                    $number = 'ORD-' . date('YmdHis') . '-' . str_pad((string)microtime(true) * 10000, 8, '0', STR_PAD_LEFT);
                    break;
                }
            } while (self::where('order_number', $number)->exists());
        } catch (\Exception $e) {
            // If order_number column doesn't exist or query fails, generate a unique number
            $number = 'ORD-' . date('YmdHis') . '-' . strtoupper(substr(uniqid('', true), -8));
        }

        return $number;
    }
}

