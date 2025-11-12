<?php

namespace App\Http\Controllers;

use App\Exports\CinemaExport;
use App\Models\Cinema;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        //cinema::all ngambil smw data pada tabel cinema
        //mengirim data dari controller ke view menggunakan compact
        return view('admin.cinema.index', compact('cinemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cinema.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'location'=> 'required | min:10'
        ],[
            'name.required' => 'nama bioskop harus diisi',
            'location.required'=> 'lokasi bioskop harus diisi',
            'location.min' => 'minimal 10 karakter'
        ]);
        $createData = Cinema::create([
            'name' => $request->name,
            'location' => $request->location
        ]);
        if($createData){
            return redirect()->route('admin.cinemas.index')->with('success', 'Data Berhasil Ditambahkan');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cinema = Cinema::find($id);
        return view('admin.cinema.edit', compact('cinema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'name'=>'required',
            'location'=> 'required | min:10'
        ],[
            'name.required' => 'nama bioskop harus diisi',
            'location.required'=> 'lokasi bioskop harus diisi',
            'location.min' => 'minimal 10 karakter'
        ]);
        //where untuk mencari data
        //sebelum update wajib ada where untuk cari data yg mau diupdate
        $updateData = Cinema::where('id',$id)->update([
            'name' => $request->name,
            'location' => $request->location
        ]);
        if($updateData){
            return redirect()->route('admin.cinemas.index')->with('success', 'Data Berhasil Diupdate');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $schedules = Schedule::where('cinema_id',  $id)->count();
        if ($schedules){
            return redirect()->route('admin.cinemas.index')->with('error', 'gabisa dong cuy');
        }

        Cinema::where('id', $id)->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Data Berhasil Dihapus');
    }

    public function cinemaExport()
    {
        $fillname = 'dataCinema.xlsx';
        return Excel::download(new CinemaExport, $fillname);
    }

    public function trash(){
        $cinemas = Cinema::onlyTrashed()->get();
        return view('admin.cinema.trash', compact('cinemas'));
    }

    public function restore($id){
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema-> restore();
        return redirect()->route('admin.cinemas.index')->with('success', 'bisa cu');
    }


    public function deletePermanent($id){
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema -> forceDelete();
        return redirect()->back()->with('success', 'bisa cu');
    }

    public function dataTable(){
        $cinemas = Cinema::query();
        return DataTables::of($cinemas)
            ->addIndexColumn()
            ->addColumn('btnAction', function($cinemas){
                $btnEdit = '<a href="'. route('admin.cinemas.edit', $cinemas->id) .'" class="btn btn-secondary">Edit</a>';
                $btnDelete = '<form action="'. route('admin.cinemas.delete', $cinemas->id) .'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button class="btn btn-danger" type="submit">Hapus</button>
                        </form>';
                        return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . '' . $btnDelete . '</div>';
            })
            ->rawColumns(['name','location','btnAction'])
            ->make(true);
    }

    public function cinemaList()
    {
        $cinemas = Cinema::all();
        return view('schedule.cinemas', compact('cinemas'));
    }

    public function cinemaSchedules($cinema_id)
    {
        
        $schedules = Schedule::where('cinema_id', $cinema_id)->with('movie')->whereHas('movie', function($q){
            $q->where('activated', 1);
        })->get();
        return view('schedule.cinema-schedules', compact('schedules'));
    }
}
