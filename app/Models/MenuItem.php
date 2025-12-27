<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'ingredients',
        'price',
        'image',
        'prep_time',
        'is_available',
        'allergens',
        'nutrition_info',
        'is_stock',
        'stock_quantity',
        'min_stock_level',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_stock' => 'boolean',
        'nutrition_info' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
