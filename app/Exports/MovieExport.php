<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;


class MovieExport implements FromCollection, WithHeadings, WithMapping
{
    private $key=0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //manggil data yang mw ada di excel
        return Movie::all();
    }

    public function headings():array
    {
        return ['no', 'judul film', 'durasi', 'genre', 'sutradara', 'usia min', 'poster', 'sinopsis'];
    }

    public function map($movie):array
    {
        return[
            ++$this->key,
            $movie->title,
            //cara ubah format
            //parse ngambil data
            //format menentukan format
            Carbon::parse($movie->duration)->format("H") . " jam" . Carbon::parse($movie->duration)->format('i') . " Menit",
            $movie->genre,
            $movie->direction,
            $movie->age_rating . "+",
            //asset()link buat liat gambar
            asset('storage') . "/" . $movie->poster,
            $movie->description
        ];
    }
}
