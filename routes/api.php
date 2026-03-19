<?php

use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Protected by Auth0 JWT (auth0-api guard). Valid Bearer token required.
|
*/

Route::middleware('auth:auth0-api')->group(function (): void {
    Route::get('/countries', [CountryController::class, 'index']);
});
