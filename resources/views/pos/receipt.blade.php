@extends('layouts.app')

@section('title', 'Struk Transaksi')

@push('styles')
<style>
    .receipt {
        max-width: 420px;
        margin: 0 auto;
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,.08);
        font-family: 'Courier New', monospace;
    }
    .receipt-header { text-align: center; padding-bottom: 12px; border-bottom: 2px dashed #333; }
    .receipt-header h4 { margin: 0; font-weight: bold; letter-spacing: 2px; }
    .receipt-body { padding: 12px 0; }
    .receipt-table { width: 100%; font-size: .85rem; }
    .receipt-table th { border-bottom: 1px dashed #999; padding: 4px 0; text-align: left; }
    .receipt-table td { padding: 3px 0; vertical-align: top; }
    .receipt-footer { border-top: 2px dashed #333; padding-top: 12px; text-align: center; font-size: .8rem; }
    .total-row { font-weight: bold; font-size: 1rem; }
    @media print {
        body * { visibility: hidden; }
        .receipt, .receipt * { visibility: visible; }
        .receipt { position: absolute; left: 0; top: 0; box-shadow: none; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')

<div class="text-center mb-3 no-print">
    <div class="alert alert-success d-inline-block px-4">
        <i class="bi bi-check-circle-fill"></i>
        <strong>Transaksi Berhasil!</strong>
    </div>
</div>

<div class="receipt" id="receiptArea">

    <div class="receipt-header">
        <h4>🛒 KASIRKU</h4>
        <small>Jl. Contoh No. 123, Jakarta</small><br>
        <small>Telp: 021-1234567</small>
    </div>

    <div class="receipt-body">
        <table style="width:100%; font-size:.8rem; margin-bottom:8px;">
            <tr><td>No. Invoice</td><td class="text-end">{{ $transaction->invoice_no }}</td></tr>
            <tr><td>Tanggal</td><td class="text-end">{{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Kasir</td><td class="text-end">{{ $transaction->user->name }}</td></tr>
            <tr><td>Pembayaran</td><td class="text-end">{{ strtoupper($transaction->payment_method) }}</td></tr>
        </table>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th width="30" class="text-center">Qty</th>
                    <th width="80" class="text-end">Harga</th>
                    <th width="90" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr style="border-top:1px dashed #999; margin:8px 0;">

        <table style="width:100%; font-size:.85rem;">
            <tr>
                <td>Subtotal ({{ $transaction->total_items }} item)</td>
                <td class="text-end">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->discount > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-end">-{{ number_format($transaction->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-end">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td class="text-end">{{ number_format($transaction->paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-end fw-bold">{{ number_format($transaction->change, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="receipt-footer">
        <div style="font-size:1rem; margin-bottom:4px;">*** TERIMA KASIH ***</div>
        <div>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
        <div style="margin-top:8px;">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
    </div>

</div>

<div class="text-center mt-3 no-print">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Cetak Struk
    </button>
    <a href="{{ route('pos.index') }}" class="btn btn-success">
        <i class="bi bi-cart-plus"></i> Transaksi Baru
    </a>
    <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary">
        <i class="bi bi-graph-up"></i> Lihat Laporan
    </a>
</div>

@endsection