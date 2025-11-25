<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

route::get('/', [MovieController::class, 'home' ])->name('home');
route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.active');
route::get('/schedule/{movie_id}',[MovieController::class,'movieSchedule'])->name('schedules.detail');
route::get('/cinemas/list', [CinemaController::class, 'cinemaList'])->name('cinemas.list');
route::get('/cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');

route::middleware('isUser')->group(function(){
    route::get('/schedule/{scheduleId}/hours/{hourId}/ticket', [TicketController::class, 'showSeats'])->name('schedules.show_seats');
    route::prefix('/tickets')->name('tickets.')->group(function(){
        route::post('/', [TicketController::class, 'store'])->name('store');
        route::get('/{ticketId}/order', [TicketController::class, 'ticketOrderPage'])->name('order');
        route::post('/barcode', [TicketController::class, 'createBarcode'])->name('barcode');
        route::get('/{ticketId}/payment', [TicketController::class, 'ticketPaymentPage'])->name('payment.page');
        route::patch('{ticketId}/payment/update', [TicketController::class, 'updateStatusTicket'])->name('update.status');
        route::get('{ticketId}/show', [TicketController::class, 'show'])->name('show');
        route::get('{ticketId}/export/pdf', [TicketController::class, 'exportPdf'])->name('export.pdf');
        route::get('/list', [TicketController::class,'index'])->name('index');
    });
});

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
    route::get('/tickets/chart', [TicketController::class, 'dataChart'])->name('tickets.chart');
    route::get('/dashboard', function(){
        return view('admin.dashboard');
    })->name('dashboard');


    route::prefix('/cinemas')->name('cinemas.')->group(function(){
        route::get('/datatable', [CinemaController::class, 'dataTable'])->name('dataTable');
        route::get('/', [CinemaController::class, 'index'])->name('index');
        route::get('create', [CinemaController::class, 'create'])->name('create');
        route::post('/store', [CinemaController::class, 'store'])->name('store');
        //id ->parameter placeholder, mengirim data ke controller. digunakan ketika akan ngambil data spesifik
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        //put = proses update data
        route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        route::get('/export', [CinemaController::class,'cinemaExport'])->name('export');
        route::get('/trash',[CinemaController::class, 'trash'])->name('trash');
        route::patch('restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('deletePermanent');
    });

    //route admin dan staff
    route::prefix('/user')->name('users.')->group(function(){
        route::get('/datatable', [UserController::class, 'dataTable'])->name('dataTable');
        route::get('/', [UserController::class, 'index'])->name('index');
        route::get('create', [UserController::class, 'create'])->name('create');
        route::post('/store', [UserController::class, 'store'])->name('store');
        route::get('/edit{id}', [UserController::class, 'edit'])->name('edit');
        route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        route::get('/export' , [UserController::class, 'exportExcel'])->name('export');
        route::get('/trash',[UserController::class, 'trash'])->name('trash');
        route::patch('restore/{id}', [UserController::class, 'restore'])->name('restore');
        route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('deletePermanent');
    });

    route::prefix('/movies')->name('movies.')->group(function(){
        route::get('/chart', [MovieController::class, 'chart'])->name('chart');
        route::get('/datatable', [MovieController::class, 'dataTable'])->name('dataTable');
        route::get('/', [MovieController::class, 'index'])->name('index');
        route::get('create', [MovieController::class, 'create'])->name('create');
        route::post('/store', [MovieController::class, 'store'])->name('store');
        route::get('/edit/{id}',[MovieController::class,'edit'])->name('edit');
        route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        route::delete('/delete/{id}', [MovieController::class , 'destroy'])->name('delete');
        route::patch('/activate/{id}', [MovieController::class, 'activate'])->name('activate');
        route::get('/export', [MovieController::class, 'exportExcel'])->name('export');
        route::get('/trash',[MovieController::class, 'trash'])->name('trash');
        route::patch('restore/{id}', [MovieController::class, 'restore'])->name('restore');
        route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('deletePermanent');
        });
    });
        route::get('/detail/{id}', [MovieController::class, 'detail'])->name('detail');


    route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function(){
        route::get('/dashboard', function(){
            return view('staff.dashboard');
        })->name('dashboard');

        route::prefix('/schedules')->name('schedules.')->group(function(){
            route::get('/datatable', [ScheduleController::class, 'dataTable'])->name('dataTable');
            route::get('/',[ScheduleController::class, 'index'])->name('index');
            route::post('/',[ScheduleController::class, 'store'])->name('store');
            route::get('/edit/{id}', [ScheduleController::class,'edit'])->name('edit');
            route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
            route::delete('/delete/{id}', [ScheduleController::class , 'destroy'])->name('delete');
            route::get('/export', [ScheduleController::class, 'exportSchedule'])->name('exportSchedule');
            route::get('/trash',[ScheduleController::class, 'trash'])->name('trash');
            route::patch('restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
            route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('deletePermanent');
        });

        route::prefix('/promos')->name('promos.')->group(function(){
            route::get('/datatable', [PromoController::class, 'dataTable'])->name('dataTable');
            route::get('/', [PromoController::class,'index'])->name('index');
            route::get('/create', [PromoController::class, 'create'])->name('create');
            route::post('/store', [PromoController::class, 'store'])->name('store');
            route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
            route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
            route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
            route::get('/export', [PromoController::class, 'exportPromo'])->name('export');
            route::get('/trash',[PromoController::class, 'trash'])->name('trash');
            route::patch('restore/{id}', [PromoController::class, 'restore'])->name('restore');
            route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('deletePermanent');


        });
    });

route::post('/auths', [UserController::class, 'register'])->name('signup.register');
Route::post('/auth', [UserController::class, 'loginAuth'])->name('login.auth');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');



// get dipake buat nampilin halaman
// post dipake buat ngirim data ke server
//patch/put ngubah data
//delete hapus data
