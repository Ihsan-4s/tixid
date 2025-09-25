@extends('templetes.app')
@section('content')
    <div class="container my-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{Session::get('success')}}</div>

        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif
        <form action="{{ route('staff.promos.update' , $promo['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="code" class="form-label">Kode Promo</label>
                <input type="text" class="form-control" id="code" name="promo_code" value="{{ $promo->promo_code }}">
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Tipe Promo</label>
                <select class="form-select" aria-label="Default select example" name="type" required value="{{ $promo->type }}">
                    <option value="" hidden selected>Pilih tipe promo</option>
                    <option value="percent" {{ old('type', $promo->type) == 'percent' ? 'selected' : '' }}>percent</option>
                    <option value="rupiah" {{ old('type', $promo->type) == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Jumlah Potongan</label>
                <input type="number" class="form-control" id="total" name="discount" value="{{ $promo->discount }}">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
