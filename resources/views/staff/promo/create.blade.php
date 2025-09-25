@extends('templetes.app')
@section('content')
    <div class="container my-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{Session::get('success')}}</div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif
        <form action="{{ route('staff.promos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="code" class="form-label">Kode Promo</label>
                <input type="text" class="form-control @error('promo_code') is-invalid @enderror" id="code" name="promo_code">
                @error('title')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Tipe Promo</label>
                <select class="form-select" aria-label="Default select example" name="type" >
                    <option value="" hidden selected>Pilih tipe promo</option>
                    <option value="percent">percent</option>
                    <option value="rupiah">Rupiah</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Jumlah Potongan</label>
                <input type="number" class="form-control" id="total" name="discount">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
