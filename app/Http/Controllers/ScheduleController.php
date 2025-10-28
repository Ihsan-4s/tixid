<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScheduleExport;
use Yajra\DataTables\Facades\DataTables;


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

        $hours = Schedule::where('cinema_id',$request->cinema_id)->where('movie_id', $request->movie_id)->value('hours');
        // jika sudah ada datadengan bioskop dan film yang sama make ambil dara jam tsb
        $hoursBefore = $hours ?? [];
        //gabungkan dengan array jam sebelum ny dengan array jam yang baru ditambah
        $mergeHours= array_merge($hoursBefore, $request->hours);
        //jika ada jam yang duplikat ambil salah satu
        $newHours = array_unique($mergeHours);

        //updateorCreate mengubah jika sudah ada mendambah bika belum ada
        $createData = Schedule::updateOrCreate([
            'cinema_id'=> $request->cinema_id,
            'movie_id'=> $request->movie_id,
        ],[
            //data yang akan diupdate
            'price'=>$request->price,
            'hours'=>$newHours
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
    public function edit(Schedule $schedule, $id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema','movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*'=>'required|date_format:H:i'
        ],[
            'price.required'=>'harga harus diisi',
            'price.numeric'=> 'harga harus diisi angka',
            'hours.*'=>'harus ada waktu',
            'hours.*.date_format'=>'jam harus sesuai format',
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours'=> $request->hours
        ]);

        if($updateData){
            return redirect()->route('staff.schedules.index')->with('success', 'bisa cu');
        }else{
            return redirect()->back()->with('error', 'gabisa cu');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, $id)
    {
        Schedule::where('id' ,$id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'bisa cu');

    }

    public function exportSchedule()
    {
        $fillname = 'data-schedule.xlsx';
        return Excel::download(new ScheduleExport, $fillname);
    }

    public function trash(){
        $scheduleTrash = Schedule::with(['cinema', 'movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }

    public function restore($id){
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule-> restore();
        return redirect()->route('staff.schedules.index')->with('success', 'bisa cu');
    }

    public function deletePermanent($id){
        $schedule=Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success', 'bisa cu');
    }

    public function dataTable(){
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return DataTables::of($schedules)
            ->addIndexColumn()
            ->addColumn('cinema', function($schedules){
                return $schedules->cinema ? $schedules->cinema->name : '';
            })
            ->addColumn('movie', function($schedules){
                return $schedules->movie ? $schedules->movie->title : '';
            })
            ->addColumn('price', function($schedules){
            return 'Rp ' . number_format($schedules->price, 0, ',', '.');
            })
            ->addColumn('hours', function($schedules) {
            $list = '';
            foreach($schedules->hours as $hour) {
                $list .= '<li>' . $hour . '</li>';
            }
            return '<ul>' . $list . '</ul>';
        })
            ->addColumn('btnAction', function($schedules){
                $btnEdit = '<a href="'. route('staff.schedules.edit', $schedules->id) .'" class="btn  btn-primary">Edit</a>';
                $btnDelete = '<form action="'. route('staff.schedules.delete', $schedules->id) .'" method="POST">
                            '. csrf_field() .'
                            '.method_field('DELETE').'
                            <button class="btn btn-danger">Hapus</button>
                        </form>';
                return '<div class="d-flex justify-content-center gap-2">' . $btnEdit . ' ' . $btnDelete . '</div>';
            })
            ->rawColumns(['cinema', 'movie', 'btnAction', 'hours', 'price'])
            ->make(true);
    }
}
