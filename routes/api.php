<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiteStatusController;

Route::post('/site-status', [SiteStatusController::class, 'store'])
    ->name('api.site-status');




