<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_source',
        'table_id',
        'waiter_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_city',
        'customer_postal_code',
        'status',
        'order_time',
        'served_at',
        'subtotal',
        'tax_amount',
        'service_charge',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'delivery_time',
        'delivery_notes',
        'payment_method',
        'payment_status',
        'priority',
        'is_urgent',
        'is_group_order',
        'tracking_number',
        'status_history',
        'external_order_id',
        'external_platform',
        'notes'
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'served_at' => 'datetime',
        'delivery_time' => 'datetime',
        'status_history' => 'array',
        'is_urgent' => 'boolean',
        'is_group_order' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function waiter()
    {
        return $this->belongsTo(Employee::class, 'waiter_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
