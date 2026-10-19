<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_no', 'user_id', 'total_items',
        'subtotal', 'discount', 'total',
        'paid', 'change', 'payment_method', 'note',
    ];

     protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total'    => 'decimal:2',
        'paid'     => 'decimal:2',
        'change'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // format rupiah
     public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    // Generate invoice number unik: INV-20250115-0001

    public static function generateInvoiceNumber(): string
{
    $date = now()->format('Ymd');
    $prefix = "INV-{$date}-";

    $last = self::where('invoice_no', 'like', $prefix . '%')
        ->orderByDesc('invoice_no')
        ->first();

    
    $sequence = $last
        ? ((int) substr($last->invoice_no, -4)) + 1
        : 1;

    return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

}
