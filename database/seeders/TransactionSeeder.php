<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $kasir = User::whereHas('roles', fn($q) => $q->where('name', 'kasir'))->first()
              ?? User::first();

        $products = Product::where('stock', '>', 5)->get();
        if ($products->isEmpty() || !$kasir) {
            $this->command->warn('⚠️  Tidak ada produk / kasir. Seeder dilewati.');
            return;
        }

        // Buat 40 transaksi dalam 7 hari terakhir
        for ($i = 0; $i < 40; $i++) {
            $dayOffset = rand(0, 6);
            $date = now()->subDays($dayOffset)->setTime(rand(8, 20), rand(0, 59));

            $items = $products->random(rand(1, 4));
            $itemsToInsert = [];
            $movements = [];
            $subtotal = 0;
            $totalItems = 0;

            foreach ($items as $product) {
                $qty = rand(1, 3);
                $itemSubtotal = $product->price * $qty;
                $subtotal += $itemSubtotal;
                $totalItems += $qty;

                $itemsToInsert[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'price'        => $product->price,
                    'qty'          => $qty,
                    'subtotal'     => $itemSubtotal,
                    'created_at'   => $date,
                    'updated_at'   => $date,
                ];

                $movements[] = [
                    'product_id'   => $product->id,
                    'user_id'      => $kasir->id,
                    'type'         => 'out',
                    'qty'          => $qty,
                    'stock_before' => $product->stock,
                    'stock_after'  => $product->stock - $qty,
                    'note'         => 'Penjualan kasir (seed)',
                    'created_at'   => $date,
                    'updated_at'   => $date,
                ];
            }

            $paid = ceil($subtotal / 5000) * 5000;

            DB::transaction(function () use ($kasir, $date, $subtotal, $totalItems, $paid, $itemsToInsert, $movements) {
                $trx = Transaction::create([
                    'invoice_no'     => 'INV-' . $date->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'user_id'        => $kasir->id,
                    'total_items'    => $totalItems,
                    'subtotal'       => $subtotal,
                    'discount'       => 0,
                    'total'          => $subtotal,
                    'paid'           => $paid,
                    'change'         => $paid - $subtotal,
                    'payment_method' => collect(['cash','cash','debit','qris'])->random(),
                    'created_at'     => $date,
                    'updated_at'     => $date,
                ]);

                foreach ($itemsToInsert as &$item) {
                    $item['transaction_id'] = $trx->id;
                }
                $trx->items()->insert($itemsToInsert);
                StockMovement::insert($movements);
            });
        }

        $this->command->info('✅ 40 transaksi dummy berhasil dibuat untuk 7 hari terakhir!');
    }
}