<?php

namespace App\Http\Controllers;

use App\Exports\CinemaExport;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


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
        Cinema::where('id', $id)->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Data Berhasil Dihapus');
    }

    public function cinemaExport()
    {
        $fillname = 'dataCinema.xlsx';
        return Excel::download(new CinemaExport, $fillname);
    }
}
