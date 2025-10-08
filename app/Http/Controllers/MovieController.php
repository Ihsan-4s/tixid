<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;

class MovieController extends Controller
{

    // where(): untuk mencari data format yang digunakan where('kolom', 'operator', 'nilai')
    // get() : mengambil semua data hasil filter
    // first() : mengambil 1 data pertama hasil filter
    // paginate(): membagi data menjadi beberapa halaman
    // orderBy : untuk mengurutkan data format orderBy('field', 'type')
    //type asc untuk mengurutkan dari kecil ke besar, lama ke baru
    //type desc untuk mengurutkan dari besar ke kecil, baru ke lama
    //limit mengambil data dengan jumlah tertentu formatnya limit(jumlah data)
    public function home(){
        $movies = Movie::where('activated',1)->orderBy('created_at', 'desc')->limit(4)->get();
        return view('home', compact('movies'));
    }

    public function detail($id){
        $movie = Movie::find($id);
        return view('schedule.detail-film', compact('movie'));
    }

    public function homeMovies(){
        $movies=Movie::where('activated',1)->orderBy('created_at','desc')->get();
        return view('movies', compact('movies'));
    }

    public function movieSchedule($movie_id){
        $movie = Movie::where('id', $movie_id)->with(['schedules','schedules.cinema'])->first();
        return view('schedule.detail-film',compact('movie'));
    }

    public function activate($id){
        $movie = movie::find($id);
        $movie->activated = !$movie->activated;
        $movie->save();
        return redirect()->route('admin.movies.index')->with('success', 'Status film berhasil diubah');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title'=>'required',
            'duration'=>'required',
            'genre'=>'required',
            'direction'=>'required',
            'age_rating'=>'required',
            'poster'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'=>'required|min:20'
        ],[
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'direction.required' => 'Sutradara film harus diisi',
            'age_rating.required' => 'Rating usia harus diisi',
            'poster.required' => 'Poster film harus diunggah',
            'poster.image' => 'File yang diunggah harus berupa gambar',
            'poster.mimes' => 'Format poster harus jpeg, png, jpg, gif, atau svg',
            'poster.max' => 'Ukuran poster maksimal 2MB',
            'description.required' => 'Deskripsi film harus diisi',
            'description.min' => 'Deskripsi minimal 20 karakter'
        ]);

        $poster = $request->file('poster');
        $namafile = rand(1, 100) . "poster." . $poster->getClientOriginalExtension();
        $path = $poster->storeAs("poster",$namafile, 'public');


        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'direction' => $request->direction,
            'age_rating' => $request->age_rating,
            'poster' => $path,
            'description' => $request->description,
            'activated' => 1
        ]);
        if($createData){
            return redirect()->route('admin.movies.index')->with('success', 'Data Berhasil Ditambahkan');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required',
            'duration'=>'required',
            'genre'=>'required',
            'direction'=>'required',
            'age_rating'=>'required',
            'poster'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'=>'required|min:20'
        ],[
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'direction.required' => 'Sutradara film harus diisi',
            'age_rating.required' => 'Rating usia harus diisi',
            'poster.required' => 'Poster film harus diunggah',
            'poster.image' => 'File yang diunggah harus berupa gambar',
            'poster.mimes' => 'Format poster harus jpeg, png, jpg, gif, atau svg',
            'poster.max' => 'Ukuran poster maksimal 2MB',
            'description.required' => 'Deskripsi film harus diisi',
            'description.min' => 'Deskripsi minimal 20 karakter'
        ]);

        $movie = Movie::find($id);
        if($request->file('poster')){
            $posterSebelumnya = storage_path('app/public/'.$movie->poster);
            if(file_exists($posterSebelumnya)){
                unlink($posterSebelumnya);
        }
        $poster = $request->file('poster');
        $namafile = rand(1, 100) . "poster." . $poster->getClientOriginalExtension();
        $path = $poster->storeAs("poster",$namafile, 'public');
    }

        $createData = Movie::Where('id', $id)->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'direction' => $request->direction,
            'age_rating' => $request->age_rating,
            'poster' => $path??$movie->poster,
            'description' => $request->description,
            'activated' => 1
        ]);
        if($createData){
            return redirect()->route('admin.movies.index')->with('success', 'Data Berhasil Diubah');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Diubah');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $movie = Movie::find( $id);

        if($movie->poster && storage::disk('public')->exists($movie->poster )){
            storage::disk('public')->delete($movie->poster);
        }
        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Data Berhasil Dihapus');
    }

    public function exportExcel()
    {
        //nama file yang akan terunduh
        $fillname = 'data-film.xlsx';
        return Excel::download(new MovieExport, $fillname);
    }


}
