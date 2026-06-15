<?php

use App\Http\Controllers\Api\IncomingCallController;
use Illuminate\Support\Facades\Route;

Route::post('/voip/incoming-call', IncomingCallController::class);
