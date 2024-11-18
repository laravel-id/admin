<?php

use App\Http\Controllers\OAuth\DropboxController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (config('app.demo')) {
        return redirect()->route('filament.admin.auth.login');
    }

    return view('welcome');
});

Route::get('/dropbox', DropboxController::class)->name('callback.dropbox');
