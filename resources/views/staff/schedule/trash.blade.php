@extends('templetes.app')
@section('content')
    <div class="container my-3">
        <div class="d-flex justify-content-end">
            <a class="btn btn-secondary" href="{{ route('staff.schedules.index') }}">Kembali</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <h3 class="my-3">Data sampah</h3>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Judul Film</th>
                <th>Aksi</th>
            </tr>

            @foreach ($scheduleTrash as $index => $schedule)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $schedule['cinema']['name'] ?? ''}}</td>
                    <td>{{ $schedule['movie']['title'] ?? ''}}</td>
                    <td class="d-flex ">
                        <form action="{{ route('staff.schedules.restore', $schedule->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-secondary">Kembalikan</button>
                        </form>
                        <form action="{{ route('staff.schedules.deletePermanent', $schedule->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
            @endforeach

        </table>
    </div>
@endsection
