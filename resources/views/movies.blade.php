@extends('templetes.app')

@section('content')
    <div class="container my-5">
        <h5 class="mb-5">seluruh film</h5>
        <form action="" method="GET">
            @csrf
            <div class="row">
                <div class="col-10">
                    <input type="text" name="search_movie" placeholder="cari judul film.." class="form-control">
                </div>
                <div class="col-2">
                    <button class="btn btn-primary" type="submit">cari</button>
                </div>
            </div>

        </form>
        <div class="d-flex justify-content-center gap-2 my-3">
            @foreach ( $movies as $movie )
            <div class="card" style="width: 13rem;">
                <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}" style="height: 300px; object-fit:cover;" class="card-img-top">
                <div class="card-body" style="padding: 0;" !important>
                    <p class="card-text text-center bg-primary py-2"><a href="{{ route('schedules.detail' , $movie->id) }}" class="text-warning"><b>beli tiket</b></a></p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
