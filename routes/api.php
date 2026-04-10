<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketApiController;

Route::get('/tickets', [TicketApiController::class, 'index']);
Route::post('/tickets', [TicketApiController::class, 'store']);
