<?php

use App\Http\Controllers\PanganController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PanganController::class, 'index']);
