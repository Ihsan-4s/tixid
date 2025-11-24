@extends('templetes.app')
@section('content')
    <div class="container card p-4 my-5">
        <div class="card-body">
            <h5 class="text-center">RINGKASAN ORDER</h5>
            <div class="d-flex">
                <img class="me-3" src="{{ asset('storage/' . $ticket['schedule']['movie']['poster'] ?? '') }}" width="120"
                    alt="">
                <div>
                    <b class="text-warning">{{ $ticket['schedule']['cinema']['name'] }}</b>
                    <br>
                    <b>{{ $ticket['schedule']['movie']['title'] }}</b>
                    <table>
                        <tr>
                            <td>Genre</td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['genre'] }}</td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['duration'] }}</td>
                        </tr>
                        <tr>
                            <td>Sutradara</td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['director'] }}</td>
                        </tr>
                        <tr>
                            <td>Usia Minimal</td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['age_rating'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div>
                <hr>
                <b class="text-secondary">NOMOR PESANAN : {{ $ticket['id'] }}</b>
                <br>
                <b>Detail Pesanan</b>
                <table>
                    <tr>
                        <td>Tiket {{ $ticket['quantity'] }}</td>
                        <td style="padding: 0 20px"></td>
                        <td><b>{{ implode(', ', $ticket['rows_of_seats']) }}</b></td>
                    </tr>
                    <tr>
                        <td>Harga Tiket</td>
                        <td style="padding: 0 20px"></td>
                        <td><b>{{ number_format($ticket['schedule']['price'], 0, ',', '.') }} <span
                                    class="text-secondary">x {{ $ticket['quantity'] }}</span></b></td>
                    </tr>
                    <tr>
                        <td>Biaya Layanan</td>
                        <td style="padding: 0 20px"></td>
                        <td><b>Rp. 4.000</b> <span class="text-secondary">x {{ $ticket['quantity'] }}</span></td>
                    </tr>
                </table>
                <b>Gunakan Promo</b>
                <select name="" id="promo_id" class="form-select" onchange="selectPromo(this)">
                    @if (count($promos) < 1)
                        <option disabled hidden selected value="">Tidak ada promo tersedia</option>
                    @else
                        <option disabled hidden selected value="">Pilih Promo</option>
                        @foreach ($promos as $promo)
                            <option value="{{ $promo['id'] }}">{{ $promo['promo_code'] }} -
                                {{ $promo['type'] == 'percent' ? $promo['discount'] . '%' : 'Rp. ' . number_format($promo['discount'], 0, ',', '.') }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
    <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}" id="ticket_id">
    <div class="w-100 p-2 text-center fixed-bottom" style="background: #112646; color:white; cursor:pointer" onclick="createQR()">
        <i class="fa-solid fa-ticket"></i> BAYAR SEKARANG
    </div>
@endsection
@push('script')
    <script>
        let promoId = null;
        function selectPromo(element){
            promoId = element.value;
        }

        function createQR(){
            let data = {
                _token: '{{ csrf_token() }}',
                promo_id: promoId,
                ticket_id: $('#ticket_id').val(),
            }
            $.ajax({
                url: '{{ route('tickets.barcode') }}',
                type: 'POST',
                data: data,
                success: function(response){
                    const ticketId = response.data.ticket_id;
                    window.location.href = `/tickets/${ticketId}/payment`;
                },
                error:function(xhr){
                    alert('Gagal membuat barcode, silahkan coba lagi');
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
@endpush
