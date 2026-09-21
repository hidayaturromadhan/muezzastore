@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Buyer Dashboard</h5>
        <p class="mb-0">Halo <b>{{ auth()->user()->username }}</b> (role: {{ auth()->user()->role }})</p>
    </div>
</div>
@endsection
