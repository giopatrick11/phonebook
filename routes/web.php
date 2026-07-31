<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route::view('/', 'welcome')->name('home');
Route::livewire('/home', 'phonebook')
    ->name('phonebook');

Route::livewire('/login', 'login')
    ->middleware('guest')
    ->name('login');

Route::livewire('/admin/contacts', 'manage-contacts')
    ->middleware('auth')
    ->name('contacts.manage');

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');
