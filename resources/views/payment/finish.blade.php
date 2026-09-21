@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">

        {{-- Title --}}
        <div class="text-center mb-6">
            <div class="text-5xl mb-3">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-800">
                Pembayaran Selesai
            </h1>
            <p class="text-gray-500 mt-1">
                Terima kasih telah melakukan transaksi.
            </p>
        </div>

        {{-- Order ID --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-4 border">
            <div class="text-sm text-gray-500 mb-1">Order ID</div>
            <div class="font-mono text-lg font-semibold text-gray-800">
                {{ $order->midtrans_order_id }}
            </div>
        </div>

        {{-- Status --}}
        @php
            $statusColor = 'bg-yellow-100 text-yellow-700';
            if (in_array($order->status, ['paid','fulfilled'])) {
                $statusColor = 'bg-green-100 text-green-700';
            }
            if ($order->status === 'failed') {
                $statusColor = 'bg-red-100 text-red-700';
            }
        @endphp

        <div class="mb-6">
            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColor }}">
                {{ strtoupper($order->status) }}
            </span>
        </div>

        {{-- Token / SN --}}
        @if(!empty($order->digiflazz_sn))
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                <div class="text-green-700 font-semibold mb-2">
                    <i class="fas fa-bolt mr-1"></i> Token / Serial Number
                </div>

                <div class="bg-white border rounded-lg p-4 text-center">
                    <div class="text-xl font-mono tracking-widest text-gray-800 break-all">
                        {{ $order->digiflazz_sn }}
                    </div>
                </div>

                <div class="text-sm text-gray-500 mt-3">
                    Simpan token ini dengan baik. Jangan dibagikan ke orang lain.
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
                <div class="text-blue-700 font-semibold mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Sedang Diproses
                </div>

                <div class="text-sm text-gray-600">
                    Hasil Digiflazz akan muncul setelah proses selesai.
                    Jika baru selesai bayar, tunggu beberapa saat lalu cek status order.
                </div>
            </div>
        @endif

        {{-- Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.show', $order) }}"
               class="flex-1 text-center px-6 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-cyan-500 to-cyan-600 hover:shadow-lg transition">
                <i class="fas fa-search mr-1"></i> Lihat Status Order
            </a>

            <a href="{{ route('home') }}"
               class="flex-1 text-center px-6 py-3 rounded-xl font-semibold border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                Kembali ke Produk
            </a>
        </div>

    </div>
</div>
@endsection
