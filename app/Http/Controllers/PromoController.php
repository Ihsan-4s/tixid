<?php

namespace App\Http\Controllers;

use App\Exports\PromoExport;
use App\Models\Promo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index' , compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promo_code'=>'required',
            'type'=>'required',
            'discount'=>'required|numeric|min:1',
        ],[
            'promo_code.required' => 'kode promo harus diisi',
            'type.required' => 'tipe wajib diisi',
            'discount.required' => 'jumlah wajib diisi',
        ]);

    if ($request->type === 'percent' && $request->discount > 100) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Discount percent tidak boleh lebih dari 100');
    } elseif ($request->type === 'rupiah' && $request->discount < 1000) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Discount rupiah tidak boleh kurang dari 1000');
    }

        $createData = Promo::create([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'activated' => 1,
        ]);

        if($createData){
            return redirect()->route('staff.promos.index')->with('success', 'Data Berhasil Ditambahkan');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promo = Promo::find($id);
        return view('staff.promo.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'promo_code'=>'required',
            'type'=>'required',
            'discount'=>'required|numeric|min:1',
        ],[
            'promo_code.required' => 'kode promo harus diisi',
            'type.required' => 'tipe wajib diisi',
            'discount.required' => 'jumlah wajib diisi',
        ]);

        if ($request->type === 'percent' && $request->discount > 100) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Discount percent tidak boleh lebih dari 100');
    }

        $promo = Promo::find($id);
        $promo->promo_code = $request->promo_code;
        $promo->type = $request->type;
        $promo->discount = $request->discount;
        $updateData = $promo->save();

        if($updateData){
            return redirect()->route('staff.promos.index')->with('success', 'Data Berhasil Diupdate');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Promo::where('id',$id)->delete();
        return redirect()->route('staff.promos.index')->with('success', 'Data Berhasil Dihapus');
    }

    public function exportPromo()
    {
        $fillname = 'data-promo.xlsx';
        return Excel::download(new PromoExport, $fillname);
    }

    public function trash(){
        $promoTrash = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promoTrash'));
    }

    public function restore($id){
        $promo = Promo::onlyTrashed()->find($id);
        $promo-> restore();
        return redirect()->route('staff.promos.index')->with('success', 'Data Berhasil Direstore');
    }

    public function deletePermanent($id){
        $promo=Promo::onlyTrashed()->find($id);
        $promo->forceDelete();
        return redirect()->back()->with('success', 'Data Berhasil Dihapus Permanen');
    }

    public function dataTable(){
        $promos = Promo::query()->get();
        return DataTables::of($promos)
        ->addIndexColumn()
        ->addColumn('btnAction', function($promos){
            $btnEdit = '<a href="'. route('staff.promos.edit' , $promos['id']) .'" class="btn btn-primary">Edit</a>';
            $btnDelete ='<form method="POST" action="'. route('staff.promos.delete' , $promos['id']) .'" >
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button class="btn btn-danger ms-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center">'.$btnEdit . $btnDelete.'</div>';
        })
        ->addColumn('discount_display', function($promos){
            if($promos->type === 'percent'){
                return $promos->discount . '%';
            } elseif($promos->type === 'rupiah'){
                return 'Rp ' . number_format($promos->discount, 0, ',', '.');
            }
            return $promos->discount;
        })
        ->rawColumns(['btnAction', 'discount_display'])
        ->make(true);
    }
}
