@extends('templetes.app')

@section('content')
    <div class="container">
        <h5 class="my-3">Grafik Pembelian Tiket</h5>
        @if (Session::get('success'))
            <div class="alert alert-success">{{Session::get('success')}} <b>selamat datang {{Auth::user()->name}}</b></div>
        @endif
    </div>
@endsection
