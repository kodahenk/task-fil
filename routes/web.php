<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('artisan', function () {
    Artisan::call('key:generate');
});

Route::get('/', function () {
    return view('welcome');
});
