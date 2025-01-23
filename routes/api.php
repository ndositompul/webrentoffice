<?php

use App\Http\Controllers\Api\BookingTransactionController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\OfficeSpaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api_key')->group(function () {

    //2 cara route yang digunakan sebagai endpoint
    Route::get('/city/{city:slug}', [CityController::class, 'show']);
    Route::apiResource('/cities', CityController::class); 

    Route::get('/office/{officeSpace:slug}', [OfficeSpaceController::class, 'show']);
    Route::apiResource('/offices', OfficeSpaceController::class);
    
    //proses untuk penyimpanan data booking
    Route::post('/booking-transaction', [BookingTransactionController::class, 'store']);
    
    //digunakan oleh customer, untuk mencocokan data yang ada di db
    //dengan data yang mereka input
    //apakah data yang mereka input sudah cocok atau belum
    Route::post('/check-booking', [BookingTransactionController::class, 'booking_details']); 
});