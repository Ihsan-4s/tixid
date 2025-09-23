<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    //mendaftarkan softdeletes
    use SoftDeletes;

    //mendaftarkan detail data (colom) agar data bisa diisi
    protected $fillable = ['user_id', 'schedule_id','promo_id','date','rows_of_seats','quantity','total_price','activated'];
}
