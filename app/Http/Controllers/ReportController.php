<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan Penjualan
     */
    public function sales(Request $request)
    {
        // Default: bulan ini
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        // Query dasar
        $baseQuery = Transaction::whereDate('created_at', '>=', $from)
                                ->whereDate('created_at', '<=', $to);

        // ===== STATISTIK UTAMA =====
        $summary = [
            'total_transactions' => (clone $baseQuery)->count(),
            'total_revenue'      => (clone $baseQuery)->sum('total'),
            'total_items_sold'   => (clone $baseQuery)->sum('total_items'),
            'avg_per_transaction'=> (clone $baseQuery)->avg('total') ?? 0,
        ];

        // ===== GRAFIK PENJUALAN HARIAN =====
        $dailySales = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== TOP 10 PRODUK TERLARIS =====
        $topProducts = TransactionItem::select(
                'product_id',
                'product_name',
                'product_sku',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', function ($q) use ($from, $to) {
                $q->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to);
            })
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // ===== PERFORMA PER KASIR =====
        $byCashier = (clone $baseQuery)
            ->select('user_id', DB::raw('COUNT(*) as trx_count'), DB::raw('SUM(total) as revenue'))
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->get();

        // ===== PER METODE PEMBAYARAN =====
        $byPayment = (clone $baseQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get();

        // ===== TRANSAKSI TERBARU (list) =====
        $transactions = (clone $baseQuery)
            ->with('user', 'items')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('reports.sales', compact(
            'from', 'to', 'summary',
            'dailySales', 'topProducts',
            'byCashier', 'byPayment', 'transactions'
        ));
    }

    /**
     * Laporan Stok
     */
    public function stock(Request $request)
    {
        $query = Product::with('category');

        // Filter kategori
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // Filter status stok
        $status = $request->input('status');
        if ($status === 'low') {
            $query->where('stock', '<=', 5);
        } elseif ($status === 'out') {
            $query->where('stock', 0);
        } elseif ($status === 'safe') {
            $query->where('stock', '>', 5);
        }

        $products = $query->orderBy('stock')->get();

        // Statistik
        $stats = [
            'total_products'   => Product::count(),
            'total_stock'      => Product::sum('stock'),
            'total_value'      => Product::sum(DB::raw('stock * cost_price')),
            'total_sell_value' => Product::sum(DB::raw('stock * price')),
            'low_stock'        => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock'     => Product::where('stock', 0)->count(),
        ];

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('reports.stock', compact('products', 'stats', 'categories'));
    }

    /**
     * Export laporan penjualan ke CSV
     */
    public function exportSales(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $transactions = Transaction::with('user', 'items')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->get();

        $filename = "laporan-penjualan-{$from}-to-{$to}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'No Invoice', 'Tanggal', 'Kasir',
                'Total Item', 'Subtotal', 'Diskon', 'Total',
                'Bayar', 'Kembalian', 'Metode Bayar'
            ]);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->invoice_no,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->user->name,
                    $t->total_items,
                    $t->subtotal,
                    $t->discount,
                    $t->total,
                    $t->paid,
                    $t->change,
                    strtoupper($t->payment_method),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Detail 1 transaksi
     */
    public function showTransaction(Transaction $transaction)
    {
        $transaction->load('items', 'user');
        return view('reports.transaction-detail', compact('transaction'));
    }
}