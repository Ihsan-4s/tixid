@extends('templetes.app')
@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <h5 class="text-center">Selesaikan Pembayaran</h5>
            <img src="{{ asset('storage/' . $ticket['ticket_payment']['qrcode']) }}" alt="" class="d-block mx-auto">
            <div class="w-25 d-block mx-auto mb-4">
                <table class="w-100">
                    <tr>
                        <td>Ticket {{$ticket['quantity']}}</td>
                        <td>{{implode(', ', $ticket['rows_of_seats'])}}</td>
                    </tr>
                    <tr>
                        <td>Kursi Reguler</td>
                        <td><b>Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }} <span class="text-secondary">+{{$ticket['quantity']}}</span> </b></td>
                    </tr>
                    <tr>
                        <td>Biaya Layanan</td>
                        <td><b>Rp. 4.000 <span class="text-secondary">x {{$ticket['quantity']}}</span> </b></td>
                    </tr>
                    <tr>
                        <td>Promo</td>
                        @php
                            if($ticket['promo']){
                                $promo = $ticket['promo']['type'] == 'percent' ? $ticket['promo']['discount'].'%' : 'Rp. '.number_format($ticket['promo']['discount'], 0, ',', '.');
                            }else{
                                $promo = 'Tidak ada promo';
                            }
                        @endphp
                        <td><b>{{ $promo }}</b></td>
                    </tr>
                    <tr>
                        <td>Total Pembayaran</td>
                        <td><b>Rp. {{ number_format($ticket['total_price'], 0, ',', '.') }}</b></td>
                    </tr>
                </table>
                <hr>
                @php
                    $price = $ticket['total_price'] + $ticket['service_fee'];
                @endphp
                <div class="d-flex justify-content-end">
                    <b>Rp. {{ number_format($price, 0, ',', '.') }}</b>
                </div>
            </div>
            <form method="POST" action="{{ route('tickets.update.status', $ticket['id']) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary btn-block btn-lg">Bayar</button>
            </form>
        </div>
    </div>
@endsection
