@extends('templetes.app')

@section('content')
    <div class="container mt-3">
        @if (Session::get('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.cinemas.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5>data bioskop</h5>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
            {{-- $cinemas dari compact di controller --}}
            @foreach ($cinemas as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['location']}}</td>
                    <td class="d-flex">
                        <a href="{{ route('admin.cinemas.edit', $item['id']) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('admin.cinemas.delete', $item['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
