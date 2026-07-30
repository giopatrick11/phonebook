<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');
Route::livewire('/home', 'phonebook')->name('phonebook');
Route::livewire('/admin/contacts', 'manage-contacts')->name('contacts.manage');
