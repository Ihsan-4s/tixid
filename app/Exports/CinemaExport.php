<?php

namespace App\Exports;

use App\Models\Cinema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class CinemaExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $key=0;
    public function collection()
    {
        return Cinema::all();
    }

    public function headings():array
    {
        return['no', 'nama bioskop', 'lokasi'];
    }

    public function map($cinema):array
    {
        return[
            ++$this->key,
            $cinema->name,
            $cinema->location
        ];
    }
}
