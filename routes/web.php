<?php

use Illuminate\Support\Facades\Route;

// SPA: serve Vue app for all frontend routes (Vue Router handles path)
Route::get('/{path?}', function () {
    return view('app');
})->where('path', '^(?!api|storage|up|callback).*$')->name('spa');

// Auth0 callback is handled by Laravel backend (Auth0 SDK redirects here for web login)
// For SPA we use the same path so the Vue app loads and @auth0/auth0-vue can process the hash/query
Route::get('/callback', function () {
    return view('app');
})->name('callback');
