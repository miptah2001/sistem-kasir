<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Tampilkan halaman utama stok (form + history)
     */
    public function index(Request $request)
{
    $products = Product::orderBy('name')->get();

    // Query history
    $query = StockMovement::with('product', 'user');

    // Filter: produk
    if ($productId = $request->input('product_id')) {
        $query->where('product_id', $productId);
    }

    // Filter: tipe
    if ($type = $request->input('type')) {
        $query->where('type', $type);
    }

    // Filter: tanggal
    if ($from = $request->input('from')) {
        $query->whereDate('created_at', '>=', $from);
    }
    if ($to = $request->input('to')) {
        $query->whereDate('created_at', '<=', $to);
    }

    // PENTING: urutkan created_at DESC, lalu id DESC (tie-breaker)
    // Biar data dengan timestamp sama tetap punya urutan konsisten
    $query->orderByDesc('created_at')->orderByDesc('id');

    // Per-page: default 10, bisa ubah lewat ?per_page=25
    $perPage = (int) $request->input('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

    $movements = $query->paginate($perPage)->withQueryString();

    // Statistik
    $stats = [
        'total_in'  => StockMovement::where('type', 'in')->sum('qty'),
        'total_out' => StockMovement::where('type', 'out')->sum('qty'),
        'today_in'  => StockMovement::where('type', 'in')->whereDate('created_at', today())->sum('qty'),
        'today_out' => StockMovement::where('type', 'out')->whereDate('created_at', today())->sum('qty'),
    ];

    return view('stocks.index', compact('movements', 'products', 'stats', 'perPage'));
}

    /**
     * Simpan pergerakan stok
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type'       => ['required', 'in:in,out'],
            'qty'        => ['required', 'integer', 'min:1'],
            'note'       => ['nullable', 'string', 'max:255'],
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'qty.min'             => 'Jumlah minimal 1.',
        ]);

        try {
            DB::transaction(function () use ($data) {
                // Lock produk biar tidak race condition
                $product = Product::lockForUpdate()->findOrFail($data['product_id']);

                // Validasi stok keluar tidak boleh minus
                if ($data['type'] === 'out' && $product->stock < $data['qty']) {
                    throw new \Exception("Stok tidak cukup. Stok tersedia: {$product->stock}.");
                }

                $stockBefore = $product->stock;

                // Update stok produk
                if ($data['type'] === 'in') {
                    $product->stock += $data['qty'];
                } else {
                    $product->stock -= $data['qty'];
                }
                $product->save();

                // Simpan riwayat
                StockMovement::create([
                    'product_id'   => $product->id,
                    'user_id'      => auth()->id(),
                    'type'         => $data['type'],
                    'qty'          => $data['qty'],
                    'stock_before' => $stockBefore,
                    'stock_after'  => $product->stock,
                    'note'         => $data['note'] ?? null,
                ]);
            });

            return back()->with('success', 'Pergerakan stok berhasil dicatat.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus riwayat stok (admin only)
     */
    public function destroy(StockMovement $stock)
    {
        // Hati-hati: hapus riwayat tidak mengembalikan stok.
        // Kita hanya izinkan hapus untuk keperluan koreksi data.
        $stock->delete();

        return back()->with('success', 'Riwayat stok berhasil dihapus.');
    }
}