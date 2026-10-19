@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">Edit Data Karyawan</h2>
            @include('admin.karyawan.form', ['karyawan' => $karyawan])
        </div>
    </div>
@endsection
