<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    //mendaftarkan softdeletes
    use SoftDeletes;

    //mendaftarkan detail data (colom) agar data bisa diisi
    protected $fillable = ['title', 'genre', 'duration','direction','description', 'age_rating','poster','activated'];

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }
}
