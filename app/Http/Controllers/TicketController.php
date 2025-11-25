<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\Promo;
use App\Models\TicketPayment;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class TicketController extends Controller
{
    public function showSeats($scheduleId, $hourId)
    {
        $schedule = Schedule::find($scheduleId);
        $hour = $schedule['hours'][$hourId] ?? '';

        $soldSeats = Ticket::where('schedule_id', $scheduleId)->where('activated', 1)->where('date', now()->format('Y-m-d'))->pluck('rows_of_seats');

        $soldSeatsFormat = [];
        foreach($soldSeats as $key => $seat){
            foreach($seat as $item){
                array_push( $soldSeatsFormat, $item);
            }
        }
        //pluck ambil dan disatukan ke array
        // dd($soldSeatsFormat);
        return view('schedule.row-seats', compact('schedule', 'hour', 'soldSeatsFormat'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth::user()->id;
        $ticketActive = Ticket::where('user_id', $userId)->where('activated', 1)->where('date', now()->format('Y-m-d'))->get();
        $ticketNonActive = Ticket::where('user_id' , $userId)->where('date', '<>',now()->format('Y-m-d'))->get();
        return view('ticket.index', compact('ticketActive', 'ticketNonActive'));


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'date' => 'required',
            'rows_of_seats' => 'required',
            'quantity' => 'required',
            'total_price' => 'required',
            'hour' => 'required',
            'service_fee' => 'required',
        ]);

        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'date' => $request->date,
            'rows_of_seats' => $request->rows_of_seats,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'hour' => $request->hour,
            'service_fee' => $request->service_fee,
            'activated' => 0,
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $createData
        ]);
    }

    public function ticketOrderPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie'])->first();
        $promos = Promo::where('activated', 1)->get();
        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function createBarcode(Request $request)
    {
        $kodeBarcode = 'TICKET' . $request->ticket_id;
        $qrImage = QrCode::format('svg')
        ->size(300)
        ->margin(2)
        ->errorCorrection('H')
        ->generate($kodeBarcode);

        $filename = $kodeBarcode . '.svg';
        $path = 'barcodes/' . $filename;
        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $request->ticket_id,
            'qrcode' => $path,
            'status' => 'processed',
            'booked_date' => now(),
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $totalPrice = $ticket->total_price;
        if($request->promo_id != null){
            $promo = Promo::find($request->promo_id);
            if($promo['type'] == 'percent'){
                $discount = $ticket['total_price'] * $promo['discount'] / 100;
            }else{
                $discount = $promo['discount'];
            }
            $totalPrice = $ticket['total_price'] - $discount;
        }

        $updateTicket = Ticket::where('id', $request->ticket_id)->update([
            'promo_id' => $request->promo_id,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Berhasil',
            'data' => $createData,
        ]);
    }

    public function ticketPaymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule',  'promo', 'ticket_payment'])->first();
        return view('schedule.payment', compact('ticket'));
    }

    public function updateStatusTicket($ticketId)
    {
        $updatePayment = TicketPayment::where('ticket_id', $ticketId)->update([
            'paid_date' => now(),
        ]);
        $updateStatus = Ticket::where('id', $ticketId)->update([
            'activated' => 1,
        ]);
        return redirect()->route('tickets.show', $ticketId)->with('success', 'Pembayaran berhasil dilakukan.');
    }
    /**
     * Display the specified resource.
     */
    public function show($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.movie', 'schedule.cinema', 'ticket_payment'])->first();
        return view('schedule.ticket', compact('ticket'));
    }

    public function exportPdf($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.movie', 'schedule.cinema', 'ticket_payment'])->first()->toArray();
        view()->share('ticket', $ticket);
        $pdf = Pdf::loadView('schedule.export-pdf', compact('ticket'));
        $filename = 'Ticket' . $ticketId . '.pdf';
        return $pdf->download($filename);
    }

    public function dataChart()
    {
        // ambil data tiket yang sudah diaktivasi pada bulan ini
        $month = now()->format('m');
        //
        $tickets = Ticket::where('activated', 1)->whereHas('ticket_payment', function($q) use ($month){
            $q->whereMonth('booked_date', $month);
        })->get()->groupBy(function($ticket){
            return Carbon::parse($ticket->ticket_payment->booked_date)->format('Y-m-d');
        })->toArray();
        $labels = array_keys($tickets);
        $data = [];
        foreach($tickets as $ticketGroup){
            array_push($data, count($ticketGroup));
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
