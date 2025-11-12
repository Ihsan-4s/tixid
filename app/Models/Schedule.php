<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    //mendaftarkan softdeletes
    use SoftDeletes;

    //mendaftarkan detail data (colom) agar data bisa diisi
    protected $fillable = ['cinema_id', 'movie_id','hours','price'];
    //json = {}/"[]"
    //supaya format array normal
    protected function casts():array
    {
        return[
            'hours' => 'array'
        ];
    }

    public function cinema(){
        //karna schedule ada di posisi dua pakai belongsTo
        return $this->belongsTo(Cinema::class);
    }

    public function movie(){
        return $this->belongsTo(Movie::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
