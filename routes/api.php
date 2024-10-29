<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransformerController;




Route::controller(TransformerController::class)->group(function () {

    Route::post('/auth/logIn', [TransformerController::class, 'transform'])->name('user.logIn');
    Route::post('/auth/register', [TransformerController::class, 'transform'])->name('user.register');



    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logOut', [TransformerController::class, 'transform'])->name('user.logOut');
        Route::get('/allUsers', [TransformerController::class, 'transform'])->name('user.allUsers');
        Route::post('/createGroup', [TransformerController::class, 'transform'])->name('group.createGroup');
        Route::post('/uploadFiles', [TransformerController::class, 'transform'])->name('file.uploadFiles');
        Route::get('/getMyFiles', [TransformerController::class, 'transform'])->name('file.getMyFiles');


    });
});
