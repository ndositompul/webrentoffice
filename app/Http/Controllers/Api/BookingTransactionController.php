<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Http\Resources\Api\BookingTransactionResource;
use App\Http\Resources\Api\ViewBookingResource;
use App\Models\BookingTransaction;
use App\Models\OfficeSpace;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class BookingTransactionController extends Controller
{
    //method check transaction data
    public function booking_details(Request $request){
        
        $request->validate([
            'phone_number' => 'required|string',
            'booking_trx_id' => 'required|string',
        ]);

        $booking = BookingTransaction::where('phone_number', $request->phone_number)
            ->where('booking_trx_id', $request->booking_trx_id)
            ->with(['officeSpace', 'officeSpace.city'])
            ->first();

        if(!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        return new ViewBookingResource($booking);
    }


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
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

        //Create the message with linke breaks
        $messageBody = "Hi {$bookingTransaction->name}, Terima kasih telah melakukan booking kantor di Web Rental Office.\n\n";
        $messageBody .= "Pesanan kantor {$bookingTransaction->officeSpace->name} Anda sedang kami proses dengan booking
        TRX ID: {$bookingTransaction->booking_trx_id}.\n\n";
        $messageBody .= "Kami akan menginformasikan kembali situs pemesanan Anda secepat mungkin."; 

        //send with feature message SMS
        // $message = $twilio->messages->create(
        //     "+{$bookingTransaction->phone_number}", //to
        //     [
        //         "body" => $messageBody,
        //         "from" => getenv("TWILIO_PHONE_NUMBER")
        //     ]
        // );

        //send with feature message WhatsApp
        $message = $twilio->messages
        ->create("whatsapp:+{$bookingTransaction->phone_number}", // to
            array(
            "from" => "whatsapp:+14155238886",
            "body" => $messageBody,
            )
        );

        //Mengembalikan response hasil transaksi
        $bookingTransaction->load('officeSpace');
        return new BookingTransactionResource($bookingTransaction);
        
    }
}
