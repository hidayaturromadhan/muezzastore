@extends('layouts.admin')

@section('title', 'Order #' . $order->midtrans_order_id)
@section('page_title', 'Order Detail')
@section('page_subtitle', 'Manajemen transaksi dan log provider')

@section('header_actions')
    <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    
    {{-- Kolom Kiri: Informasi Utama --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Transaksi</h3>
                @include('admin.partials.status-badge', ['status' => $order->status])
            </div>
            <div class="card-body">
                <div class="table-wrap">
                    <table class="admin-table">
                        <tbody>
                            <tr>
                                <th>Order ID</th>
                                <td class="fw-bold text-cyan">{{ $order->midtrans_order_id }}</td>
                            </tr>
                            <tr>
                                <th>Pelanggan</th>
                                <td>
                                    <div class="fw-semibold">{{ $order->user?->username ?? 'Guest' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $order->user?->email }}</div>
                                </td>
                            </tr>
                            <tr>
                                <th>Produk</th>
                                <td>
                                    <div class="fw-semibold">{{ $order->product?->product_name }}</div>
                                    <code class="badge badge-gray">{{ $order->product?->buyer_sku_code }}</code>
                                </td>
                            </tr>
                            <tr>
                                <th>Target / No. Tujuan</th>
                                <td><span class="badge badge-cyan" style="font-size: 0.9rem;">{{ $order->target }}</span></td>
                            </tr>
                            <tr>
                                <th>Total Bayar</th>
                                <td class="fw-bold" style="font-size: 1.1rem; color: var(--navy);">
                                    Rp {{ number_format((int)$order->gross_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <th>ID Ref Digiflazz</th>
                                <td><code>{{ $order->digiflazz_trx_id ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <th>Waktu Transaksi</th>
                                <td>{{ $order->created_at->format('d M Y, H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div style="margin-top: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px dashed var(--border);">
                    <div class="fw-bold mb-3" style="font-size: 0.8rem; text-transform: uppercase; color: var(--gray);">Aksi Manajemen</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                        <form action="{{ route('admin.orders.checkDigiflazz', $order) }}" method="POST">
                            @csrf
                            <button class="btn btn-cyan btn-sm" type="submit" {{ empty($order->digiflazz_trx_id) ? 'disabled' : '' }}>
                                <i class="fas fa-sync"></i> Sync Digiflazz
                            </button>
                        </form>

                        <form action="{{ route('admin.orders.retry', $order) }}" method="POST" onsubmit="return confirm('Retry request akan membuat ref_id baru. Lanjutkan?');">
                            @csrf
                            <button class="btn btn-outline-cyan btn-sm" type="submit">
                                <i class="fas fa-redo"></i> Retry Fulfill
                            </button>
                        </form>

                        <form action="{{ route('admin.orders.markFailed', $order) }}" method="POST" onsubmit="return confirm('Tandai transaksi sebagai gagal secara manual?');">
                            @csrf
                            <button class="btn btn-danger btn-sm" type="submit">
                                <i class="fas fa-times-circle"></i> Mark Failed
                            </button>
                        </form>
                    </div>
                    <div class="text-muted mt-3" style="font-size: 0.7rem; line-height: 1.4;">
                        <i class="fas fa-info-circle"></i> <strong>Tips:</strong> Gunakan <i>Sync</i> untuk memperbarui status tanpa order ulang. Gunakan <i>Retry</i> jika saldo sempat tidak cukup atau gangguan provider.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: JSON Logs --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        {{-- Midtrans Payload --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Midtrans Payload</h3>
                <button class="btn btn-ghost btn-sm" onclick="copyToClipboard('midtrans-json')"><i class="far fa-copy"></i></button>
            </div>
            <div class="card-body" style="padding: 0;">
                <pre id="midtrans-json" style="margin:0; padding: 1.25rem; background: #1e293b; color: #e2e8f0; font-size: 0.75rem; max-height: 300px; overflow: auto; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">{{ json_encode($order->midtrans_payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>

        {{-- Digiflazz Response --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Digiflazz Response</h3>
                <button class="btn btn-ghost btn-sm" onclick="copyToClipboard('digi-json')"><i class="far fa-copy"></i></button>
            </div>
            <div class="card-body" style="padding: 0;">
                <pre id="digi-json" style="margin:0; padding: 1.25rem; background: #1e293b; color: #38bdf8; font-size: 0.75rem; max-height: 300px; overflow: auto; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">{{ json_encode($order->digiflazz_response, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Payload berhasil disalin!');
        });
    }
</script>
@endpush