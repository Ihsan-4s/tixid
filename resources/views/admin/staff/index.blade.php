@extends('templetes.app')

@section('content')
    <div class="container mt-3">
        @if (Session::get('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mb-3">Data Pengguna (admin / staff)</h5>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            @php $no = 1; @endphp
            @foreach ($users as $item)
                @if (in_array($item['role'], ['admin', 'staff']))
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['email'] }}</td>
                        <td>
                            @if ($item['role'] == 'admin')
                                <span class="badge badge-primary">Admin</span>
                            @elseif ($item['role'] == 'staff')
                                <span class="badge badge-success">staff</span>
                            @endif
                        </td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('admin.users.edit',$item['id']) }}" class="btn btn-info mx-2">Edit</a>
                            <form action="{{ route('admin.users.delete', $item['id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach


        </table>
    </div>
@endsection
