<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'sku', 'name',
        'cost_price', 'price', 'stock',
        'image', 'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
    //Helper: cek stok rendah

    public function islowStock(int $threshold = 5): bool
    {
        return $this->stock <= $threshold;
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
