<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Halaman utama kasir
     */
    public function index()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        // Riwayat transaksi hari ini (khusus user yang login)
        $todayTransactions = Transaction::with('items')
            ->where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->take(5)
            ->get();

        return view('pos.index', compact('products', 'todayTransactions'));
    }

    /**
     * Proses checkout
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'exists:products,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'paid'           => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,debit,qris'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'note'           => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Keranjang tidak boleh kosong.',
            'items.min'      => 'Minimal 1 produk untuk checkout.',
            'paid.min'       => 'Uang bayar harus lebih dari 0.',
        ]);

        try {
            $transaction = DB::transaction(function () use ($data) {
                $subtotalAll = 0;
                $totalItems = 0;
                $itemsToInsert = [];
                $movementsToInsert = [];

                // Loop item, lock produk satu per satu
                foreach ($data['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['id']);

                    // Validasi stok
                    if ($product->stock < $item['qty']) {
                        throw new \Exception(
                            "Stok '{$product->name}' tidak cukup. Tersedia: {$product->stock}, diminta: {$item['qty']}."
                        );
                    }

                    // Validasi produk aktif
                    if (!$product->is_active) {
                        throw new \Exception("Produk '{$product->name}' tidak aktif.");
                    }

                    $subtotal = $product->price * $item['qty'];
                    $subtotalAll += $subtotal;
                    $totalItems += $item['qty'];

                    $itemsToInsert[] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'product_sku'  => $product->sku,
                        'price'        => $product->price,
                        'qty'          => $item['qty'],
                        'subtotal'     => $subtotal,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    $movementsToInsert[] = [
                        'product_id'   => $product->id,
                        'user_id'      => auth()->id(),
                        'type'         => 'out',
                        'qty'          => $item['qty'],
                        'stock_before' => $product->stock,
                        'stock_after'  => $product->stock - $item['qty'],
                        'note'         => 'Penjualan kasir',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    // Update stok
                    $product->decrement('stock', $item['qty']);
                }

                // Hitung diskon & total
                $discount = $data['discount'] ?? 0;
                if ($discount > $subtotalAll) {
                    throw new \Exception('Diskon tidak boleh lebih besar dari subtotal.');
                }
                $total = $subtotalAll - $discount;

                // Validasi pembayaran
                if ($data['payment_method'] === 'cash' && $data['paid'] < $total) {
                    throw new \Exception('Uang bayar kurang dari total.');
                }

                $change = $data['paid'] - $total;

                // Buat transaksi
                $trx = Transaction::create([
                    'invoice_no'     => Transaction::generateInvoiceNumber(),
                    'user_id'        => auth()->id(),
                    'total_items'    => $totalItems,
                    'subtotal'       => $subtotalAll,
                    'discount'       => $discount,
                    'total'          => $total,
                    'paid'           => $data['paid'],
                    'change'         => $change,
                    'payment_method' => $data['payment_method'],
                    'note'           => $data['note'] ?? null,
                ]);

                // Simpan items
                foreach ($itemsToInsert as &$item) {
                    $item['transaction_id'] = $trx->id;
                }
                $trx->items()->insert($itemsToInsert);

                // Simpan stock movements
                $trx->items()->get(); // refresh
                foreach ($movementsToInsert as &$mv) {
                    $mv['reference'] = $trx->invoice_no;
                }
                StockMovement::insert($movementsToInsert);

                return $trx;
            });

            return redirect()
                ->route('pos.receipt', $transaction)
                ->with('success', 'Transaksi berhasil!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman struk setelah checkout
     */
    public function receipt(Transaction $transaction)
    {
        $transaction->load('items', 'user');
        return view('pos.receipt', compact('transaction'));
    }
}