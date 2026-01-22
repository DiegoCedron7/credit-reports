<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;





Route::get('/reports/credit/export', [SubscriptionController::class, 'export']);


