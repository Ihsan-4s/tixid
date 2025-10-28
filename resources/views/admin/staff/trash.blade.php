@extends('templetes.app')
@section('content')
    <div class="container my-3">
        <div class="d-flex justify-content-end">
            <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Kembali</a>
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

            @foreach ($users as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        @if ($item->role === 'admin')
                            <span class="badge bg-primary-subtle text-primary">Admin</span>
                        @elseif ($item->role === 'staff')
                            <span class="badge bg-success-subtle text-success">Staff</span>
                        @endif
                    </td>
                    <td class="d-flex ">
                        <form action="{{ route('admin.users.restore', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-secondary">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.users.deletePermanent', $item->id) }}" method="POST">
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
