@extends('templetes.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5 p-4">
        <nav data-mdb-navbar-init class="navbar navbar-expand-lg bg-body-tertiary mb-3 p-3">
            <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Pengguna</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Tambah</a></li>
                    </ol>
                </nav>
            </div>
        </nav>
        <form method="POST" class="container bg-light shadow p-3 mb-5 bg-body-tertiary rounded"
            action="{{ route('admin.users.store') }}">
            <h3 class="text-center my-5">Buat Data Staff</h3>
            @csrf
            <div class="mb-3 mx-2">
                <label for="name" class="form-label "> nama lengkap</label>
                <input type="text" class="form-control @error('name') is-invalid
                @enderror"
                    id="name" name="name">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3 mx-2">
                <label for="location" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid
                @enderror"
                    id="name" name="email">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3 mx-2">
                <label for="location" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid
                @enderror"
                    id="name" name="password">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary mx-2 my-2" type="submit">Tambah Data</button>
            </div>
        </form>
    </div>
@endsection
