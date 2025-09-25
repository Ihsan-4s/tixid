@extends('templetes.app')
@section('content')

    <div class="container my-5">
        @if (Session::get('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>

        @endif

        @if (Session::get('error'))
        <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif

        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">export cuyy</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Promo</a>
        </div>
        <h5>promo</h5>

        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
            @php $no = 1; @endphp
            @foreach ($promos as $promo)
                <tr>
                    <th>{{ $no++ }}</th>
                    <th>{{ $promo['promo_code'] }}</th>
                    <td>
                        @if ($promo->type == 'percent')
                            {{ $promo->discount }} %
                        @elseif ($promo->type == 'rupiah')
                            Rp {{ number_format($promo->discount, 0, ',', '.') }}
                        @endif
                    </td>
                    <th class="d-flex">
                        <a href="{{ route('staff.promos.edit' , $promo['id']) }}" class="btn btn-primary">Edit</a>
                        <form method="POST" action="{{ route('staff.promos.delete' , $promo['id']) }}" >
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger ms-2">Hapus</button>
                        </form>
                    </th>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
