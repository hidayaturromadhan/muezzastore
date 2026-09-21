@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page_title', 'Users')
@section('page_subtitle', 'Kelola hak akses dan peran pengguna sistem')

@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Pengguna</h3>
        <span class="badge badge-gray">{{ $users->total() }} Total User</span>
    </div>
    <div class="card-body p-0">
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th class="text-center">Role Saat Ini</th>
                        <th style="width: 350px;">Aksi Ubah Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        @php
                            $actor = auth()->user();
                            $isSelf = $actor->id === $u->id;
                            $isAdminTarget = $u->role === 'admin';
                            $disabled = $isSelf || $isAdminTarget;
                            
                            // Ambil inisial untuk avatar jika tidak ada foto
                            $initials = strtoupper(substr($u->username, 0, 2));
                        @endphp

                        <tr class="{{ $isSelf ? 'bg-light' : '' }}">
                            <td class="text-center text-muted small">{{ $u->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-navy">
                                            {{ $u->username }} 
                                            @if($isSelf) 
                                                <span class="badge badge-cyan" style="font-size: 0.6rem;">ANDA</span> 
                                            @endif
                                        </div>
                                        <div class="text-muted small">ID: #USR-{{ 1000 + $u->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td class="text-center">
                                @if($u->role === 'admin')
                                    <span class="badge badge-green">
                                        <i class="fas fa-shield-alt me-1"></i> {{ strtoupper($u->role) }}
                                    </span>
                                @else
                                    <span class="badge badge-gray">
                                        <i class="fas fa-user me-1"></i> {{ strtoupper($u->role) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($disabled)
                                    <div class="d-flex align-items-center gap-2 text-muted small px-2">
                                        <i class="fas fa-lock"></i>
                                        <span>Proteksi Peran: Tidak dapat diubah</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('admin.users.role', $u) }}" class="d-flex gap-2">
                                        @csrf
                                        <select name="role" class="form-select form-select-sm" style="border-radius: 8px;">
                                            @foreach(['buyer' => 'Buyer / Pelanggan', 'admin' => 'Administrator'] as $val => $label)
                                                <option value="{{ $val }}" {{ $u->role === $val ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-cyan btn-sm px-3" type="submit">
                                            Update
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3" style="opacity: 0.2;"></i>
                                <p>Data user tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $users->links() }}
</div>

@endsection

@push('styles')
<style>
    /* Inisial Avatar */
    .avatar-circle {
        width: 38px;
        height: 38px;
        background: var(--navy);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Override table wrap */
    .table-wrap {
        overflow-x: auto;
    }

    /* Khusus tr jika user adalah diri sendiri */
    tr.bg-light {
        background-color: #f8fafc !important;
    }

    /* Badge kustom tambahan */
    .badge-gray {
        background: #e2e8f0;
        color: #475569;
    }
</style>
@endpush