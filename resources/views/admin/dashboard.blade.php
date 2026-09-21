@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan transaksi & status sistem')

@section('header_actions')
    <a href="{{ route('admin.orders.index') }}" class="btn btn-cyan btn-sm">
        <i class="fas fa-receipt"></i> Semua Order
    </a>
@endsection

@section('content')

    {{-- ── KPI GRID ──────────────────────────────────── --}}
    <div class="kpi-grid">

        {{-- Saldo Digiflazz --}}
        <div class="kpi-card">
            <div class="kpi-icon navy"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="kpi-label">Saldo Digiflazz</div>

                @php
                    $saldo = $balanceValue ?? null;
                    $isLow = is_int($saldo) && $saldo < 100000; // warning < 100k
                @endphp

                @if($saldo === null)
                    <div class="kpi-value" style="font-size:1.25rem;">--</div>
                    <div class="kpi-sub" style="color:#ef4444;">gagal ambil saldo</div>
                @else
                    <div class="kpi-value" style="{{ $isLow ? 'color:#dc2626;' : '' }}">
                        Rp {{ number_format((int)$saldo, 0, ',', '.') }}
                    </div>
                    <div class="kpi-sub">
                        update: {{ $balanceUpdatedAt ? \Carbon\Carbon::parse($balanceUpdatedAt)->format('H:i:s') : '-' }}
                        @if($isLow)
                            • <span style="color:#dc2626;font-weight:700;">saldo rendah</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon cyan"><i class="fas fa-calendar-day"></i></div>
            <div>
                <div class="kpi-label">Order Hari Ini</div>
                <div class="kpi-value">{{ $todayOrders }}</div>
                <div class="kpi-sub">transaksi masuk</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="kpi-label">Fulfilled</div>
                <div class="kpi-value">{{ $fulfilled }}</div>
                <div class="kpi-sub">berhasil terkirim</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon cyan"><i class="fas fa-credit-card"></i></div>
            <div>
                <div class="kpi-label">Paid</div>
                <div class="kpi-value">{{ $paid }}</div>
                <div class="kpi-sub">menunggu proses</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon amber"><i class="fas fa-spinner"></i></div>
            <div>
                <div class="kpi-label">Processing</div>
                <div class="kpi-value">{{ $processing }}</div>
                <div class="kpi-sub">sedang diproses</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon navy"><i class="fas fa-clock"></i></div>
            <div>
                <div class="kpi-label">Pending</div>
                <div class="kpi-value">{{ $pending }}</div>
                <div class="kpi-sub">belum dibayar</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon red"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="kpi-label">Failed</div>
                <div class="kpi-value">{{ $failed }}</div>
                <div class="kpi-sub">gagal diproses</div>
            </div>
        </div>

    </div>

    {{-- ── BOTTOM GRID: Revenue + Quick Actions ──────── --}}
    <div class="bottom-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

        {{-- Revenue Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Estimasi Revenue</h3>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:flex-end;gap:.75rem;margin-bottom:1rem;">
                    <div>
                        <div style="font-size:.75rem;font-weight:600;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">
                            Total (Paid + Fulfilled)
                        </div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:2rem;font-weight:800;color:var(--navy);line-height:1;">
                            Rp {{ number_format((int) $profit, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Mini stat row --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;padding-top:1rem;border-top:1px solid var(--border);">
                    <div style="background:#f8fafc;border-radius:10px;padding:.75rem;">
                        <div style="font-size:.7rem;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Fulfilled</div>
                        <div style="font-size:1.125rem;font-weight:800;color:#16a34a;">{{ $fulfilled }}</div>
                        <div style="font-size:.7rem;color:var(--gray);">order sukses</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:10px;padding:.75rem;">
                        <div style="font-size:.7rem;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Paid</div>
                        <div style="font-size:1.125rem;font-weight:800;color:var(--cyan);">{{ $paid }}</div>
                        <div style="font-size:.7rem;color:var(--gray);">perlu diproses</div>
                    </div>
                </div>

                <p style="font-size:.72rem;color:#94a3b8;margin-top:.875rem;line-height:1.5;">
                    * Revenue dihitung dari <code style="background:#f1f5f9;padding:.1rem .3rem;border-radius:4px;font-size:.7rem;">gross_amount</code>
                    order berstatus <strong>paid</strong> dan <strong>fulfilled</strong>.
                </p>
            </div>
        </div>

        {{-- Quick Actions Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi Cepat</h3>
            </div>
            <div class="card-body">

                <div style="display:flex;flex-direction:column;gap:.625rem;">

                    @if($processing > 0)
                        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}"
                           style="display:flex;align-items:center;justify-content:space-between;padding:.875rem 1rem;background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;text-decoration:none;transition:background .2s;">
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <div style="width:34px;height:34px;border-radius:9px;background:#fef9c3;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:.875rem;">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div>
                                    <div style="font-size:.8125rem;font-weight:700;color:#92400e;">Lihat Processing</div>
                                    <div style="font-size:.7rem;color:#b45309;">{{ $processing }} order perlu dicek</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:#d97706;font-size:.75rem;"></i>
                        </a>
                    @endif

                    @if($failed > 0)
                        <a href="{{ route('admin.orders.index', ['status' => 'failed']) }}"
                           style="display:flex;align-items:center;justify-content:space-between;padding:.875rem 1rem;background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;text-decoration:none;transition:background .2s;">
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <div style="width:34px;height:34px;border-radius:9px;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:.875rem;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div>
                                    <div style="font-size:.8125rem;font-weight:700;color:#991b1b;">Lihat Failed</div>
                                    <div style="font-size:.7rem;color:#b91c1c;">{{ $failed }} order gagal</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:#dc2626;font-size:.75rem;"></i>
                        </a>
                    @endif

                    @if($paid > 0)
                        <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}"
                           style="display:flex;align-items:center;justify-content:space-between;padding:.875rem 1rem;background:rgba(3,174,198,0.06);border:1.5px solid rgba(3,174,198,0.2);border-radius:12px;text-decoration:none;transition:background .2s;">
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <div style="width:34px;height:34px;border-radius:9px;background:rgba(3,174,198,0.12);display:flex;align-items:center;justify-content:center;color:var(--cyan);font-size:.875rem;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>
                                    <div style="font-size:.8125rem;font-weight:700;color:var(--navy);">Lihat Paid</div>
                                    <div style="font-size:.7rem;color:var(--gray);">{{ $paid }} belum diproses</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--cyan);font-size:.75rem;"></i>
                        </a>
                    @endif

                    <a href="{{ route('admin.products.index') }}"
                       style="display:flex;align-items:center;justify-content:space-between;padding:.875rem 1rem;background:#f8fafc;border:1.5px solid var(--border);border-radius:12px;text-decoration:none;transition:background .2s;">
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div style="width:34px;height:34px;border-radius:9px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:var(--gray);font-size:.875rem;">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <div style="font-size:.8125rem;font-weight:700;color:var(--navy);">Kelola Produk</div>
                                <div style="font-size:.7rem;color:var(--gray);">Tambah, edit, nonaktifkan</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right" style="color:var(--gray);font-size:.75rem;"></i>
                    </a>

                </div>
            </div>
        </div>

    </div>

@push('styles')
<style>
    @media (max-width: 768px) {
        .bottom-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@endsection
