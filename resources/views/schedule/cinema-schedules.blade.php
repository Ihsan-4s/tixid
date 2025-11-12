@extends('templetes.app')

@section('content')
    <div class="container my-5 card">
        <div class="card-body">
            <i class="fa-solid fa-location-dot me-3"></i>{{ $schedules[0]['cinema']['location'] }}
            <hr>
            @foreach ($schedules as $schedule)
                <div class="my-2">
                    <div class="d-flex">
                        <div style="width: 150px; height:200px">
                            <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}" alt="" class="w-100">
                        </div>
                        <div class="ms-5 mt-4">
                            <h5>{{ $schedule['movie']['title'] }}</h5>
                            <table>
                                <tr>
                                    <td><b class="text-secondary">Genre</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['genre'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Durasi</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['duration'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Sutradara</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['direction'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Rating Usia</b></td>
                                    <td class="px-3"></td>
                                    <td class="badge badge-danger">+{{ $schedule['movie']['age_rating'] }}</td>
                                </tr>
                            </table>

                        </div>
                    </div>
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-end">
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>
                        <div class="d-flex gap-3 ps-3 my-2">
                            @foreach ($schedule['hours'] as $index => $hours)
                                {{-- this digunakan untuk ngirim element html yang di click ke js nya --}}
                                <div class="btn btn-outline-secondary"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}',this)">
                                    {{ $hours }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
    <div class="w-100 p-2 text-center fixed-bottom" id="wrapper-btn">
        <a href="" id="btn-ticket"><i class="fa solid fa-ticket"></i> BELI TIKET</a>
    </div>
@endsection

@push('script')
    <script>
        let selectedHours = null;
        let selectedSchedule = null;
        let lastClickedElement = null;

        function selectedHour(scheduleId, hourId, el) {
            //memindahkan data dari parameter ke variable luar
            selectedHours = hourId;
            selectedSchedule = scheduleId;

            if (lastClickedElement) {
                lastClickedElement.style.background = '';
                lastClickedElement.style.color = '';
                lastClickedElement.style.borderColor = '';
            }
            el.style.background = '#112646';
            el.style.color = 'white';
            el.style.borderColor = '#112646';
            //update last clicked element
            lastClickedElement = el;

            let btnWrapper = document.querySelector('#wrapper-btn');
            let btnTicket = document.querySelector('#btn-ticket');
            btnWrapper.style.background = '#112646';
            btnTicket.style.color = 'white';
            btnWrapper.style.borderColor = '#112646';

            //set url route
            let url = "{{ route('schedules.show_seats', ['scheduleId' => ':schedule', 'hourId' => ':hour']) }}"
                .replace(':schedule', scheduleId)
                .replace(':hour', hourId);
            //replace untuk mengganti schedule dan hour jadi data yang sebenarnya
            btnTicket.href = url;
        }
    </script>
@endpush
