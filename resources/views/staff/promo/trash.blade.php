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
                <th>Kode</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>

            @foreach ($promoTrash as $index => $promo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $promo['promo_code'] ?? ''}}</td>
                    <td>
                        @if ($promo->type == 'percent')
                            {{ $promo['discount'] ?? ''}} %
                        @elseif ($promo->type == 'rupiah')
                            Rp {{ number_format($promo['discount'] ?? '', 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="d-flex ">
                        <form action="{{ route('staff.promos.restore', $promo->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-secondary">Kembalikan</button>
                        </form>
                        <form action="{{ route('staff.promos.deletePermanent', $promo->id) }}" method="POST">
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
