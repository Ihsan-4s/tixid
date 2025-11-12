<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use App\Models\Schedule;
use Yajra\DataTables\Facades\DataTables;


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

    public function homeMovies(Request $request){
        //pengambilan data dari input search
        $nameMovie = $request->search_movie;
        //jika namaMovie di isi
        if($nameMovie !=""){
            // like mencari data yang mirip atau mengandung teks
            $movies = Movie::where('title', 'LIKE', '%' . $nameMovie . '%')->where('activated', 1)->orderBy('created_at', 'desc')->get();
        }else{
            $movies=Movie::where('activated',1)->orderBy('created_at','desc')->get();
        }
        return view('movies', compact('movies'));

    }

    public function movieSchedule($movie_id, Request $request){
        $sortirHarga = $request->sortirHarga;
        if($sortirHarga == 'ASC'){
            $movie = Movie::where('id', $movie_id)->with(['schedules'=>function($q) use($sortirHarga){
                $q->orderBy('price', $sortirHarga);

            },'schedules.cinema'])->first();
        }else{
            $movie = Movie::where('id', $movie_id)->with(['schedules','schedules.cinema'])->first();
        }

        $sortirAlfabet = $request->sortirAlfabet;
        if($sortirAlfabet == 'ASC'){
            $movie->schedules = $movie->schedules->sortBy(function($schedule){
                return $schedule->cinema->name;
            })->values();
        }elseif($sortirAlfabet == 'DESC'){
            $movie->schedules = $movie->schedules->sortByDesc(function($schedule){
                return $schedule->cinema->name;
            })->values();
        }
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
        $movie = Schedule::where('movie_id',  $id)->count();
        if ($movie){
            return redirect()->route('admin.movies.index')->with('error', 'gabisa dong cuy');
        }

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

    public function trash(){
        $movies = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movies'));
    }

    public function restore($id){
        $movie = Movie::withTrashed()->find($id);
        $movie-> restore();
        return redirect()->route('admin.movies.index')->with('success', 'Data Berhasil Direstore');
    }

    public function deletePermanent($id){
        $movie=Movie::onlyTrashed()->find($id);
        $movie->forceDelete();
        return redirect()->back();
    }

    public function dataTable()
    {
        $movies = Movie::query()->get();
        return DataTables::of($movies)
            ->addIndexColumn()
            ->addColumn('imgPoster', function($movie){
                $imgUrl = asset('storage/' . $movie['poster']);
                return '<img src="'.$imgUrl.'" width="120px"/>';
            })
            ->addColumn('activeBadge', function($movie){
                if($movie->activated == 1){
                    return '<span class="badge badge-success">Active</span>';
                }else{
                    return '<span class="badge badge-danger">Inactive</span>';
                }
            })
            ->addColumn('btnActions', function($movie){
                $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showModal('. json_encode($movie) .')\'>Detail</button>';
                $btnEdit = '<a href="'. route('admin.movies.edit', $movie['id']).'" class="btn btn-primary me-2">Edit</a>';

                $btnDelete = '<form action="'. route('admin.movies.delete', $movie['id']) .'" method="POST">'.
                            csrf_field().
                            method_field('DELETE').'
                        <button class="btn btn-danger">Hapus</button>
                        </form>';

                        if($movie['activated'] == 1){
                            $btnNonAktif = '<form action="'. route('admin.movies.activate', $movie['id']) .'" method="POST">'.
                            csrf_field().
                            method_field('PATCH').'
                        <button class="btn btn-warning">Non Aktif</button>
                        </form>';
                        }else {
                            $btnNonAktif = '';
                        }

                        return '<div class="d-flex gap-2">'. $btnDetail . $btnEdit . $btnDelete . $btnNonAktif .'</div>';
            })
            ->rawColumns(['imgPoster','activeBadge','btnActions'])
            ->make(true);
    }


}
