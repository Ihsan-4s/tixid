<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    //mendaftarkan softdeletes
    use SoftDeletes;

    //mendaftarkan detail data (colom) agar data bisa diisi
    protected $fillable = ['user_id', 'schedule_id','promo_id','date','rows_of_seats','quantity','total_price','activated', 'service_fee', 'hour'];

    protected function casts()
    {
        return [
            'rows_of_seats' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function ticket_payment()
    {
        return $this->hasOne(TicketPayment::class);
    }
}
