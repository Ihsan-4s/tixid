@extends('templetes.app')

@section('content')
    <div class="container my-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('error'))
        <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.trash') }}" class="btn btn-danger me-2">Trash</a>
            <a href="{{ route('admin.movies.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.movies.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mb-3">Data Film</h5>
        <table class="table table-bordered" id="tableMovie">
            <thead>
            <tr>
                <th>#</th>
                <th>Poster</th>
                <th>Judul Film</th>
                <th>Status Aktif</th>
                <th>Aksi</th>
            </tr>
            </thead>

        </table>
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalDetailLabel">Detail Film</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalDetailBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function(){
            $("#tableMovie").DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.movies.dataTable') }}',
                columns: [
                    {data : 'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false},
                    {data : 'imgPoster', name:'imgPoster', orderable:false, searchable:false},
                    {data : 'title', name:'title', orderable:true, searchable:true},
                    {data : 'activeBadge', name:'activeBadge', orderable:true, searchable:true},
                    {data : 'btnActions', name:'btnActions', orderable:false, searchable:false},
                ]
            })
        })

        function showModal(item) {
            // menyiapkan gambar
            let image = "{{ asset('storage') }}" + "/" + item.poster;

            let content = `
                <img src="${image}" width="120" class="d-block mx-auto my-3">
                <ul>
                    <li>Judul: ${item.title}</li>
                    <li>Durasi: ${item.duration}</li>
                    <li>Genre: ${item.genre}</li>
                    <li>Sutradara: ${item.director}</li>
                    <li>Usia Minimal: <span class="badge badge-danger">${item.age_rating}</span></li>
                    <li>Sinopsis: ${item.description}</li>
                </ul>
            `;

            // taruh ke modal
            let modalDetailBody = document.querySelector('#modalDetailBody');
            modalDetailBody.innerHTML = content;

            let modalDetail = document.querySelector("#modalDetail");
            // munculkan modal bootstrap modal yg id nya modaldetail
            new bootstrap.Modal(modalDetail).show();
        }
    </script>
@endpush
