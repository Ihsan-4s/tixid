<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    //mendaftarkan softdeletes
    use SoftDeletes;

    //mendaftarkan detail data (colom) agar data bisa diisi
    protected $fillable = ['name', 'location'];
}
