<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/index', [App\Http\Controllers\HomeController::class, 'booking'])->name('index');
Route::post('/bookSlots', [App\Http\Controllers\HomeController::class, 'bookSlots'])->name('bookSlots');
Route::get('/slotList', [App\Http\Controllers\HomeController::class, 'slotList'])->name('slotList');
// routes/web.php
Route::get('/search-items', [App\Http\Controllers\HomeController::class, 'search'])->name('search.items');


Route::get('/availableslot', [App\Http\Controllers\HomeController::class, 'availableslot'])->name('availableslot');
Route::post('/addslot', [App\Http\Controllers\HomeController::class, 'addslot'])->name('addslot');

Route::get('auth/google',[App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/call-back',[App\Http\Controllers\GoogleAuthController::class, 'callbackGoogle']);
