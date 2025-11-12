@extends('templetes.app')
@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <b>{{ $schedule['cinema']['name'] }}</b>
            <br>
            <b>{{ now()->format('d F, Y') }} - {{ $hour }}</b>
            <div class="alert alert-secondary">
                <i class="fa-solid fa-info text-danger"></i> Anak berusia 2 tahun ke atas wajib memiliki tiket.
            </div>
            <div class="w-50 d-block mx-auto my-4">
                <div class="row">
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height:20px; background: blue; margin-right:5px;">
                        </div>kursi dipilih
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height:20px; background: #112646; margin-right:5px;">
                        </div>kursi tersedia
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height:20px; background: #eaeaea; margin-right:5px;">
                        </div>kursi terjual
                    </div>
                </div>
            </div>
            @php
                //array buat perulangan
                $rows = range('A', 'H');
                $cols = range(1,18);
            @endphp
            @foreach ($rows as $row)
                <div class="d-flex justify-content-center align-items-center">
                    @foreach ($cols as $col)
                        @if ($col == 7)
                            <div style="width: 50px"></div>
                        @endif
                        <div style="width: 45px; height:45px; text-align:center; font-weight:bold; color:white; padding-top:10px; cursor:pointer;background:#112646; margin:5px; border-radius:8px" onclick="selectSeat('{{ $schedule->price }}','{{ $row }}','{{ $col }}', this)">
                            {{ $row }}-{{ $col }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="fixed-bottom" >
        <div class="p-4 bg-light text-center w-100"><b>LAYAR BIOSKOP</b></div>
        <div class="row w-100 bg-light">
            <div class="col-6 py-3 text-center " style="border: 1px solid grey">
                <h5>Total Harga</h5>
                <h5 id="totalPrice">Rp.-</h5>
            </div>
            <div class="col-6 py-3 text-center " style="border: 1px solid grey">
                <h5>Kursi Dipilih</h5>
                <h5 id="seats">-</h5>
            </div>
        </div>
        {{-- input hidden --}}
        <input type="hidden" id="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" id="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" id="date" value="{{ now() }}">
        <input type="hidden" id="hour" value="{{ $hour }}">
        <div class="w-100 bg-light p-2 text-center" id="btnOrder"><b>Ringkasan Order</b></div>
    </div>
@endsection
@push('script')
    <script>
        let seats = [];
        let totalPrice = 0;

        function selectSeat(price, row, col, element) {
            // buat format nomor kursi : A-10
            let seat = row + '-' + col;
            // cek ke array seats apakah kursi ini udah ada di array atau belum
            // indexOf() : mencari item di array dan mengembalikan nilai index itemnya
            let indexSeat = seats.indexOf(seat);
            // jika ada item maka index array bernilai 0 kalo gaada -1
            if (indexSeat == -1) {
                // kalau kursi tsb belum ada di arrau maka tambahkan dan warna biru
                seats.push(seat); //push tambahin item ke array
                element.style.background = 'blue';
            } else {
                // kalau kursi ada di array artinya klik kali ini untuk hapus
                seats.splice(indexSeat, 1); // splice hapus item di array berdasarkan index
                element.style.background = '#112646';
            }

            totalPrice = price * seats.length; //length : count
            let totalPriceElement = document.querySelector('#totalPrice');
            totalPriceElement.innerText = totalPrice;

            let seatsElement = document.querySelector('#seats');
            // join(',') : mengubah array jadi string dipisahkan tanda koma
            seatsElement.innerText = seats.join(', ');

            let btnOrder = document.querySelector('#btnOrder');
            if (seats.length > 0) {
                btnOrder.classList.remove('bg-light');
                btnOrder.style.background = '#112646';
                btnOrder.style.color = 'white';
                btnOrder.style.cursor = 'pointer';
                // kalau di klik lakukan proses pembuatan data tiket
                btnOrder.onclick = createTicket;
            } else {
                // classlist : mengakses class html add tambah class remove hapus class
                btnOrder.classList.add('bg-light');
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.style.cursor = '';
                btnOrder.onclick = null;
            }
        }

        function createTicket() {
            // ajax (asynchronous javascript and xml) : mengakses data di database lewat js digunakan dengan jquery ($)
            $.ajax({
                url: "{{ route('tickets.store') }}", //route proses data
                method: "POST", //http method
                data: {
                    _token: "{{ csrf_token() }}", //token csrf
                    // fillable : value, data yang akan dikirim ke BE
                    user_id: $("#user_id").val(),
                    schedule_id: $("#schedule_id").val(),
                    date: $("#date").val(),
                    hour: $("#hour").val(),
                    rows_of_seats: seats,
                    quantity: seats.length,
                    total_price: totalPrice,
                    service_fee: 4000 * seats.length
                },
                success: function(response) {
                    // kalau berhasil mau ngapain
                    //window.location.href redirect ke halaman
                    let ticketId = response.data.id;
                    window.location.href = `/tickets/${ticketId}/order`;
                },
                error: function(message) {
                    alert('gagal membuat data tiket!');
                }
            });
        }

    </script>
@endpush
