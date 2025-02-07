<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Models\BookingTransaction;
use App\Models\OfficeSpace;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    //
    public function store(StoreBookingTransactionRequest $request)
    {
        $validatedData = $request->validated();

        $officeSpace = OfficeSpace::find($validatedData['office_space_id']);

        //cek pembayaran manual 
        $validatedData['is_paid'] = false;

        $validatedData['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();

        $validatedData['duration'] = $officeSpace->duration;
        
        //Membuat durasi waktu start booking and end booking
        $validatedData['ended_at'] = (new \DateTime($validatedData['started_at']))
        ->modify("+{$officeSpace->duration} days")->format('Y-m-d');
        //Menyimpan data booking
        $bookingTransaction = BookingTransaction::create($validatedData);

        //Mengirim notif melalui sms atau whatsapp dengan library twilio
        
    }
}
