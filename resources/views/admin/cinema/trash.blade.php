@extends('templetes.app')
@section('content')
    <div class="container my-3">
        <div class="d-flex justify-content-end">
            <a class="btn btn-secondary" href="{{ route('admin.movies.index') }}">Kembali</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <h3 class="my-3">Data sampah</h3>
        <table class="table table-bordered">
            <tr>
                <th>#</th>

                <th>Judul Film</th>
                <th>aktif</th>
                <th>aksi</th>
            </tr>

            @foreach ($cinemas as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{$item['name'] ?? ''}}</td>
                    <td>{{$item['location'] ?? ''}}</td>
                    <td class="d-flex ">
                        <form action="{{ route('admin.cinemas.restore', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-secondary">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.cinemas.deletePermanent', $item->id) }}" method="POST">
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
