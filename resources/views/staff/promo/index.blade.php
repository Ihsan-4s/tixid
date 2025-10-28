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
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success me-2">Tambah Promo</a>
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-danger">Trash</a>
        </div>
        <h5>promo</h5>

        <table class="table table-bordered" id="promoTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
            </thead>

        </table>
    </div>
@endsection
@push('script')
<script>
    $(function(){
        $('#promoTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('staff.promos.dataTable') }}',
            columns: [
                {data : 'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false},
                {data : 'promo_code', name:'promo_code', orderable:true, searchable:true},
                {data : 'discount_display', name:'discount_display', orderable:true, searchable:true},
                {data : 'btnAction', name:'btnAction', orderable:false, searchable:false},
            ]
        })
    })
</script>
@endpush
