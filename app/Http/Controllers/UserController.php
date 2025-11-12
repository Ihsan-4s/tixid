<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller
{
    public function register(Request $request){
        /**
     * Request $request ngambil value request input
     * dd() buat debugging
     */
        // dd($request->all());
        //validasi data
        $request->validate([
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required | email:dns',
            //memastikan email valid
            'password' => 'required',
        ],[
            //custom pesan format : name_input.validasi = pesan error
            'first_name.required' => 'First name wajib diisi',
            'first_name.min' => 'First name minimal 3 karakter',
            'last_name.required' => 'Last name wajib diisi',
            'last_name.min' => 'Last name minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);
        //element (fungsi model) tambah data baru = create
        $createData = User::create([
            //column request->name input
            'name' => $request->first_name . ' ' . $request->last_name,

            'email' => $request->email,
            //enskripsi data password menjadi karakter acak menggunakan hash
            'password' => Hash::make($request->password),
            //role diisi langsung user biar  agar gabisa jadi admin
            'role' => 'user'
        ]);

        if($createData){
            //redirect kalo berhasil
            return redirect()->route('login')->with('success','Register berhasil, silahkan login');
        }else{
            //redirect kalo gagal, back() balik ke halaman sebelumnya
            return redirect()->back()->with('error','Register gagal, silahkan coba lagi');
        }
    }

    public function loginAuth(Request $request){
        $request->validate([
            'email' => 'required',
            //memastikan email valid
            'password' => 'required',
        ],[
            //custom pesan format : name_input.validasi = pesan error
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);
        //nyimpen data yang akan di verifikasi
        $data = $request->only('email','password');
        //auth::attempt buat verifikasi data
        if(Auth::attempt($data)){
            if(Auth::user()->role == 'admin'){
                return redirect()->route('admin.dashboard')->with('success','berhasil login');
            }
            elseif(Auth::user()->role == 'staff'){
                return redirect()->route('staff.dashboard')->with('success','berhasil login');
            }
            return redirect()->route('home')->with('success','Login berhasil');
        }else{
            return redirect()->back()->with('error','Login gagal, silahkan coba lagi');
    }}

    public function logout(){
        Auth::logout();
        return redirect()->route('home')->with('Logout','berhasil');
    }

    public function index()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.staff.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email' => 'required',
            'password' => 'required',
        ],[
            'name.required' => 'nama harus diisi',
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $createData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);

        if($createData){
            return redirect()->route('admin.users.index')->with('success', 'Data Berhasil Ditambahkan');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.staff.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'email' => 'required',

        ],[
            'name.required' => 'nama harus diisi',
            'email.required' => 'Email wajib diisi',

        ]);

        $updateData = User::where('id',$id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        if($updateData){
            return redirect()->route('admin.users.index')->with('success', 'Data Berhasil Diubah');
        }else{
            return redirect()->back()->with('error', 'Data Gagal Ditambahkan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        User::where('id',$id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'Data Berhasil Dihapus');
    }

    public function exportExcel()
    {
        $fillname = "data-user.xlsx";
        return Excel::download(new UserExport,$fillname);
    }

    public function trash()
    {
        $users = User::onlyTrashed()->get();
        return view('admin.staff.trash', compact('users'));
    }

    public function restore($id)
    {
        User::onlyTrashed()->where('id',$id)->restore();
        return redirect()->route('admin.users.index')->with('success', 'Data Berhasil Dikembalikan');
    }

    public function deletePermanent($id)
    {
        User::onlyTrashed()->where('id',$id)->forceDelete();
        return redirect()->route('admin.users.trash')->with('success', 'Data Berhasil Dihapus Permanen');
    }

    public function dataTable(){
        $users = User::query()->whereIn('role', ['admin', 'staff'])->get();
        return DataTables::of($users)
        ->addIndexColumn()
        ->addColumn('role', function($users){
            if($users->role == 'admin'){
                $role = '<span class="badge bg-primary">Admin</span>';
            }elseif($users->role == 'staff'){
                $role = '<span class="badge bg-warning">Staff</span>';
            }
            return $role;
        })

        ->addColumn('btnAction', function($users){
            $btnEdit = '<a href="'. route('admin.users.edit', $users->id) .'" class="btn btn-info">Edit</a>';
            $btnDelete = '<form action="'. route('admin.users.delete', $users->id) .'" method="POST">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . ' ' . $btnDelete . '</div>';
        })
        ->rawColumns(['role','btnAction'])
        ->make(true);
    }
}
