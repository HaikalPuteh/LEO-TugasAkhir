<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
});

Route::get('/simulation', function () {
    return view('simulation');
});

Route::get('/dummy', function () {
    return view('dummy');
});

Route::get('/selengkapnya', function () {
    return view('selengkapnya');
});