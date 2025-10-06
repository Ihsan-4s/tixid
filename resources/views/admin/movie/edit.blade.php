@extends('templetes.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5">
        {{-- mengizinkan formulir mengrim file --}}
        <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">judul film</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ $movie['title'] }}">
                    @error('title')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="duration" class="form-label" >Durasi Film </label>
                    <input type="time" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ $movie['duration'] }}">
                    @error('duration')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="genre" class="form-label">genre</label>
                    <input type="text" name="genre" id="genre" placeholder="romantis, fantasi" class="form-control @error('genre') is-invalid @enderror" value="{{ $movie['genre'] }}">
                    @error('genre')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="direction" class="form-label">director</label>
                    <input type="text" name="direction" id="direction" class="form-control @error('direction') is-invalid @enderror" value="{{$movie['direction']}}">
                    @error('direction')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="age rating" class="form-label">age rating</label>
                    <input type="number" name="age_rating" id="age_rating" placeholder="R, SU, Dll" class="form-control @error('age_rating') is-invalid @enderror" value="{{$movie['age_rating']}}" >
                    @error('age_rating')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="poster" class="form-label">poster</label>
                    <img src="{{ asset('storage/' . $movie['poster']) }}" width="120">
                    <input type="file" name="poster" id="poster" class="form-control @error('poster') is-invalid @enderror" >
                    @error('poster')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-32">
                    <label for="description">sinopsis</label>
                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{$movie['description']}}</textarea>
                    @error('description')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>

                <button  type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </div>
@endsection
