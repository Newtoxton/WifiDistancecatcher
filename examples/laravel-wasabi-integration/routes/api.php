<?php

use App\Http\Controllers\WifiScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WiFi Distance Catcher API Routes
|--------------------------------------------------------------------------
|
| These routes handle Wi-Fi network scanning, distance calculation,
| and storage of results to Wasabi cloud storage.
|
*/

Route::prefix('wifi')->group(function () {
    // Get available Wi-Fi networks
    Route::get('/networks', [WifiScanController::class, 'getNetworks']);
    
    // Calculate distance from RSSI value
    Route::post('/calculate-distance', [WifiScanController::class, 'calculateDistance']);
    
    // Get scan history from Wasabi
    Route::get('/history', [WifiScanController::class, 'getScanHistory']);
});
