@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">Tambah Lokasi Kantor</h2>
            @include('admin.lokasi.form')
        </div>
    </div>
@endsection
