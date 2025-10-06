<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        $movies = Movie::all();
        $cinemas = Cinema::all();
        return view('staff.schedule.index', compact('schedules','movies','cinemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cinema_id'=> 'required',
            'movie_id'=>'required',
            'price' => 'required|numeric',
            'hours.*'=> 'required|date_format:H:i'
        ],[
            'cinema_id.required' => 'bioskop harus dipilih',
            'movie_id.required' => 'movie harus dipilih',
            'price.required' => 'harus ada harga',
            'price.numeric' => 'harus nomor',
            'hours.*.required' => 'harus ada waktu',
            'hours.*.date_format' => 'format harus sesuai'
        ]);

        $createData = Schedule::create([
            'cinema_id'=> $request->cinema_id,
            'movie_id'=> $request->movie_id,
            'price'=>$request->price,
            'hours'=>$request->hours
        ]);

        if($createData){
            return redirect()->route('staff.schedules.index')->with('success', 'data berhasil ditambah');
        }else{
            return redirect()->route('staff.schedules.index')->with('error', 'Data Gagal Ditambahkan');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
