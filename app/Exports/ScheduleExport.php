<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Ambil semua data schedule + relasi movie & cinema
     */
    public function collection()
    {
        return Schedule::with(['movie', 'cinema'])->get();
    }

    /**
     * Header kolom di Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Movie Title',
            'Cinema Name',
            'Show Hours',
            'Price',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Mapping data tiap baris biar tampil rapi
     */
    public function map($schedule): array
    {

        $hoursFormatted = is_array($schedule->hours) ? implode(', ', $schedule->hours): $schedule->hours;

        return [
            $schedule->id,
            $schedule->movie->title ?? '-',
            $schedule->cinema->name ?? '-',
            $hoursFormatted,
            $schedule->price,
            $schedule->created_at,
            $schedule->updated_at,
        ];
    }
}
