<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/perkenalan', function () { 
    return '<h1>Selamat Datang di project magang natusi</h1>'; 
});
