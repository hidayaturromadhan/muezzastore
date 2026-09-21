@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Muezza Shop</h4>
        <div class="text-muted small">Topup Digiflazz + Midtrans</div>
    </div>

    <div class="d-flex gap-2">
        @auth
            <span class="badge text-bg-light border">
                Login: <b>{{ auth()->user()->username }}</b> ({{ auth()->user()->role }})
            </span>

            @if(auth()->user()->isAdmin())
                <a class="btn btn-sm btn-primary" href="{{ route('admin.dashboard') }}">Admin</a>
            @else
                <a class="btn btn-sm btn-primary" href="{{ route('buyer.dashboard') }}">Buyer</a>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger" type="submit">Logout</button>
            </form>
        @else
            <a class="btn btn-sm btn-outline-primary" href="{{ route('login') }}">Login</a>
            <a class="btn btn-sm btn-primary" href="{{ route('register') }}">Register</a>
        @endauth
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Include daftar produk (reuse view products/index) --}}
@include('products.index', ['products' => $products, 'q' => $q])

@endsection
