<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransformerController;



Route::post('/auth/logIn',[TransformerController::class,'transform'])->name('user.logIn');
Route::post('/auth/logOut',[TransformerController::class,'transform'])->middleware(['auth:sanctum'])->name('user.logOut');
Route::get('/allUsers',[TransformerController::class,'transform'])->middleware(['auth:sanctum'])->name('user.allUsers');
Route::post('/auth/register',[TransformerController::class,'transform'])->name('user.register');
