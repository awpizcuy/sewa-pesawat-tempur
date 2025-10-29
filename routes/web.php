<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// Auth pages
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

// Member pages
Route::view('/units', 'units.index')->name('units.index');
Route::view('/profile', 'user.profile')->name('user.profile');
Route::view('/my-rentals', 'user.my-rentals')->name('user.my_rentals');
Route::view('/rentals/new', 'rentals.create')->name('rentals.create');

// Admin pages
Route::prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/users', 'admin.users')->name('admin.users');
    Route::view('/units', 'admin.units')->name('admin.units');
    Route::view('/categories', 'admin.categories')->name('admin.categories');
    Route::view('/rentals', 'admin.rentals')->name('admin.rentals');
});
