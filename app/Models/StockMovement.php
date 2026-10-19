<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'type',
        'qty', 'stock_before', 'stock_after',
        'note', 'reference',
    ];

    public function product()
    {
        return $this->belongsTO(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Helper untuk badge

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }

}
