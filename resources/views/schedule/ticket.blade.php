@extends('templetes.app')
@section('content')
    <div class="container my-5 card p-4">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="{{ route('tickets.export.pdf',$ticket['id']) }}" class="btn btn-secondary">Unduh ('PDF')</a>
            </div>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                @foreach ($ticket['rows_of_seats'] as $kursi)
                    <div class="p-2">
                        <div class="d-flex justify-content-end">
                            <b>{{$ticket['schedule']['cinema']['name']}}</b>
                        </div>
                        <hr>
                        <b>{{$ticket['schedule']['movie']['title']}}</b>
                        <br>
                        <p>Tanggal: {{\Carbon\Carbon::parse($ticket['ticket_payment']['booked_date'])->format('d F Y')}}</p>
                        <p>Waktu: {{\Carbon\Carbon::parse($ticket['hour'])->format('H:i')}}</p>
                        <p>Kursi: {{$kursi}}</p>
                        <p>Harga: Rp. {{number_format($ticket['schedule']['price'], 0, ',', '.')}}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
