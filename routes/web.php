<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

route::get('/schedule', function (){
    return view('schedule.detail-film');
})->name('schedules.detail');

route::get('/auth', function (){
    return view('auth.login');
})->name('login');

route::get('/auths', function (){
    return view('auth.signup');
})->name('auth.signup');

//memanggil middleware yang akan digunakan
//group() mengelompokan route agar mengikuti sifat sebelumnya
route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function(){
    //prefix gunanya untuk ditulis 1 kali bisa dipakai berkali kali
    //admin dashboard disimpan di group middleware agar dapat menggunakan middleware tsb
    route::get('/dashboard', function(){
        return view('admin.dashboard');
    })->name('dashboard');

    route::prefix('/cinemas')->name('cinemas.')->group(function(){
        route::get('/', [CinemaController::class, 'index'])->name('index');
        route::get('create', [CinemaController::class, 'create'])->name('create');
        route::post('/store', [CinemaController::class, 'store'])->name('store');
        //id ->parameter placeholder, mengirim data ke controller. digunakan ketika akan ngambil data spesifik
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        //put = proses update data
        route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        route::get('/export', [CinemaController::class,'cinemaExport'])->name('export');
    });

    //route admin dan staff
    route::prefix('/user')->name('users.')->group(function(){
        route::get('/', [UserController::class, 'index'])->name('index');
        route::get('create', [UserController::class, 'create'])->name('create');
        route::post('/store', [UserController::class, 'store'])->name('store');
        route::get('/edit{id}', [UserController::class, 'edit'])->name('edit');
        route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        route::get('/export' , [UserController::class, 'exportExcel'])->name('export');
    });

    route::prefix('/movies')->name('movies.')->group(function(){
        route::get('/', [MovieController::class, 'index'])->name('index');
        route::get('create', [MovieController::class, 'create'])->name('create');
        route::post('/store', [MovieController::class, 'store'])->name('store');
        route::get('/edit/{id}',[MovieController::class,'edit'])->name('edit');
        route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        route::delete('/delete/{id}', [MovieController::class , 'destroy'])->name('delete');
        route::patch('/activate/{id}', [MovieController::class, 'activate'])->name('activate');
        route::get('/export', [MovieController::class, 'exportExcel'])->name('export');
        });
    });
        route::get('/', [MovieController::class, 'home' ])->name('home');
        route::get('/detail/{id}', [MovieController::class, 'detail'])->name('detail');
        route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.active');

    route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function(){
        route::get('/dashboard', function(){
            return view('staff.dashboard');
        })->name('dashboard');

        route::prefix('/schedules')->name('schedules.')->group(function(){
            route::get('/',[ScheduleController::class, 'index'])->name('index');
            route::post('/',[ScheduleController::class, 'store'])->name('store');
        });

        route::prefix('/promos')->name('promos.')->group(function(){
            route::get('/', [PromoController::class,'index'])->name('index');
            route::get('/create', [PromoController::class, 'create'])->name('create');
            route::post('/store', [PromoController::class, 'store'])->name('store');
            route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
            route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
            route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
            route::get('/export', [PromoController::class, 'exportPromo'])->name('export');


        });
    });

route::post('/auths', [UserController::class, 'register'])->name('signup.register');
Route::post('/auth', [UserController::class, 'loginAuth'])->name('login.auth');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');



// get dipake buat nampilin halaman
// post dipake buat ngirim data ke server
//patch/put ngubah data
//delete hapus data
