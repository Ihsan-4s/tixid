@extends('templetes.app')
@section('content')
    <div class="container my-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.schedules.trash') }}" class="btn btn-primary me-2">Trash Bin</a>
            <a href="{{ route('staff.schedules.exportSchedule') }}" class="btn btn-secondary me-2">Export</a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah
                Data</button>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <h3 class="my-3">Data Jadwal Tayangan</h3>
        <table class="table table-bordered" id="scheduleTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Judul Film</th>
                <th>Harga</th>
                <th>Jam Tayang</th>
                <th>Aksi</th>
            </tr>
            </thead>

            {{-- @foreach ($schedules as $index => $schedule)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $schedule['cinema']['name'] }}</td>
                    <td>{{ $schedule['movie']['title'] }}</td>
                    <td>Rp {{ number_format($schedule->price, 0,',','.') }}</td>
                    <td>
                        <ul>
                            @foreach ($schedule['hours'] as $jam)
                                <li>{{ $jam }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="d-flex ">
                        <a href="{{ route('staff.schedules.edit', $schedule->id) }}" class="btn  btn-primary">Edit</a>
                        <form action="{{ route('staff.schedules.delete', $schedule->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach --}}

        </table>
    </div>
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAddLabel">Tambah Data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('staff.schedules.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">

                            <label for="cinema_id" class="col-form-label">Bioskop</label>
                            <select name="cinema_id" id="cinema_id"
                                class="form-select @error('cinema_id') is-invalid @enderror">
                                <option disabled hidden selected value="">Pilih Bioskop</option>
                                @foreach ($cinemas as $cinema)
                                    <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }}</option>
                                @endforeach

                            </select>
                            @error('cinema_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="movie_id" class="col-form-label">Film</label>
                            <select name="movie_id" id="movie_id"
                                class="form-select @error('movie_id') is-invalid @enderror">
                                <option disabled hidden selected value="">Pilih Film</option>
                                @foreach ($movies as $movie)
                                    <option value="{{ $movie['id'] }}">{{ $movie['title'] }}</option>
                                @endforeach
                            </select>
                            @error('movie_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" name="price" id="price"
                                class="form-control @error('price') is-invalid @enderror">
                            @error('price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            {{-- jika ada err validasi array hours --}}
                            @if ($errors->has('hours.*'))
                                <small class="text-danger">{{ $errors->first('hours.*') }}</small>
                            @endif
                            <label for="hours" class="form-label">Jam Tayang</label>
                            <input type="time" name="hours[]" id="hours"
                                class="form-control @if ($errors->has('hours.*')) is-invalid @endif">
                            {{-- wadah untuk penambahan input dari js --}}
                            <div id="additionalInput"></div>
                            <span class="text-primary mt-3" style="cursor: pointer" onclick="addInput()">+Tambah
                                Input</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function(){
            $('#scheduleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('staff.schedules.dataTable') }}',
                columns: [
                    {data : 'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false},
                    {data : 'cinema', name:'cinema', orderable:true, searchable:true},
                    {data : 'movie', name:'movie', orderable:true, searchable:true},
                    {data : 'price', name:'price', orderable:true, searchable:true},
                    {data : 'hours', name:'hours', orderable:true, searchable:true},
                    {data : 'btnAction', name:'btnAction', orderable:false, searchable:false},
                ]
            })
        })

        function addInput() {
            let content = `<input type="time" name="hours[]" id="hours" class="form-control mt-2">`;
            //panggil bagian yang mau diisi
            let wadah = document.querySelector("#additionalInput");
            //tambahkan konten karna akan terus bertambah pakai +=
            wadah.innerHTML += content;
        }
    </script>

    @if ($errors->any())
        <script>
            let modalAdd = document.querySelector('#modalAdd');
            new bootstrap.Modal(modalAdd).show();
        </script>
    @endif
@endpush
