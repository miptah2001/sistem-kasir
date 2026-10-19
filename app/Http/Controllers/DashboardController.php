<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama
        $today = today();
        $yesterday = today()->subDay();

        $revenueToday = Transaction::whereDate('created_at', $today)->sum('total');
        $revenueYesterday = Transaction::whereDate('created_at', $yesterday)->sum('total');
        
        // Hitung Growth
        $growth = 0;
        if ($revenueYesterday > 0) {
            $growth = (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100;
        } elseif ($revenueToday > 0) {
            $growth = 100;
        }

        $stats = [
            'total_products' => Product::count(),
            'trx_today'      => Transaction::whereDate('created_at', $today)->count(),
            'revenue_today'  => $revenueToday,
            'revenue_growth' => $growth,
            'low_stock'      => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock'   => Product::where('stock', 0)->count(),
        ];

        // 2. Data Grafik 7 Hari Terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'label'   => $date->locale('id')->isoFormat('ddd, D M'),
                'revenue' => (float) Transaction::whereDate('created_at', $date)->sum('total'),
                'count'   => Transaction::whereDate('created_at', $date)->count(),
            ];
        }

        // 3. Top 5 Produk Terlaris (Bulan Ini)
        $topProducts = TransactionItem::select(
                'product_name',
                'product_sku',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', function ($q) {
                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            })
            ->groupBy('product_name', 'product_sku')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 4. Transaksi Terbaru
        $recentTransactions = Transaction::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 5. Produk Stok Rendah
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'chartData', 'topProducts',   'recentTransactions', 'lowStockProducts'
        ));
    }
}