@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">Tambah Karyawan Baru</h2>
            @include('admin.karyawan.form')
        </div>
    </div>
@endsection
