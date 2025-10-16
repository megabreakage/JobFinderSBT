<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Examples\SearchSelectExample;

Route::get('/', function () {
    return view('welcome');
});

// Component Examples (for development/testing)
Route::get('/examples/search-select', SearchSelectExample::class)->name('examples.search-select');
