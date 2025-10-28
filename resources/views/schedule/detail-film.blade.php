@extends('templetes.app')

@section('content')
    <div class="container pt-5">
        <div class="w-75 d-block m-auto">
            <div class="d-flex">
                <div style="width: 150px; height:200px">
                    <img src="{{ asset('storage/' . $movie->poster) }}" alt="" class="w-100">
                </div>
                <div class="ms-5 mt-4">

                    <h5>{{ $movie->title }}</h5>
                    <table>
                        <tr>
                            <td><b class="text-secondary">Genre</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie->genre }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Durasi</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie->duration }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Sutradara</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie->direction }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Rating Usia</b></td>
                            <td class="px-3"></td>
                            <td class="badge badge-danger">+{{ $movie->age_rating }}</td>
                        </tr>
                    </table>

                </div>
            </div>
            <div class="w-100 row mt-5">
                <div class="col-6 pe-5">
                    <div class="d-flex flex-column justify-content-end align-items-end">
                        <div class="d-flex align-items-center">
                            <h3 class="text-warning me-2">9.2</h3>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <small>4.144 vote</small>
                    </div>
                </div>
                <div class="col-6 ps-5" style="border-left:2px solid #c7c7c7">
                    <div class="d-flex align-items-center">
                        <div class="fas fa-heart text-danger me-2"></div>
                        <b>masukan wishlist</b>
                    </div>
                    <small>9.000</small>
                </div>
            </div>
            <div class="d-flex w-100 bg-light mt-3">

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        bioskop
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Bogor</a></li>
                        <li><a class="dropdown-item" href="#">Jakarta Timur</a></li>
                        <li><a class="dropdown-item" href="#">Jakarta Barat</a></li>
                    </ul>
                </div>
                @php
                    if (request()->get('sortirHarga') == 'ASC') {
                        $sortirHarga = 'DESC';
                    }elseif (request()->get('sortirHarga') == 'DESC') {
                        $sortirHarga = 'ASC';
                    }else{
                        $sortirHarga = 'ASC';
                    }


                    if (request()->get('sortirAlfabet') == 'ASC') {
                        $sortirAlfabet = 'DESC';
                    }elseif (request()->get('sortirAlfabet') == 'DESC') {
                        $sortirAlfabet = 'ASC';
                    }else{
                        $sortirAlfabet = 'ASC';
                    }

                    
                @endphp
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        sortir
                    </button>
                    <ul class="dropdown-menu">
                        {{-- query params (?) untuk search sort and limit bisa di form atau href --}}
                        <li><a class="dropdown-item" href="?sortirAlfabet={{ $sortirAlfabet }}">Alfabet</a></li>
                        <li><a class="dropdown-item" href="?sortirHarga={{ $sortirHarga }}">Harga</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-5">
                @foreach ($movie['schedules'] as $schedule)
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="fa-solid fa-building"></i><b class="ms-2">{{ $schedule['cinema']['name'] }}</b>
                                <br>
                                <small class="ms-3">{{ $schedule['cinema']['location'] }}</small>
                            </div>
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>
                        <div class="d-flex gap-3 ps-3 my-2">
                            @foreach ($schedule['hours'] as $hours)
                                <div class="btn btn-outline-secondary">{{ $hours }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <hr>
                <div class="w-100 p-2 bg-light text-center fixed-buttom">
                    <a href=""><i class="fa solid fa-ticket"></i> BELI TIKET</a>
                </div>
            </div>
        </div>
    @endsection
