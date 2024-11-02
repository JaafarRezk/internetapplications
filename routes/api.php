<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransformerController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;




Route::controller(TransformerController::class)->group(function () {

    Route::post('/auth/logIn', [TransformerController::class, 'transform'])->name('user.logIn');
    Route::post('/auth/register', [TransformerController::class, 'transform'])->name('user.register');



    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logOut', [TransformerController::class, 'transform'])->name('user.logOut');
        Route::get('/allUsers', [TransformerController::class, 'transform'])->name('user.allUsers');
        Route::post('/createGroup', [TransformerController::class, 'transform'])->name('group.createGroup');
        Route::get('file/getMyFiles', [TransformerController::class, 'transform'])->name('file.getMyFiles');
        Route::post('file/checkIn/{id}', [TransformerController::class, 'transform'])->name('file.checkIn');
        Route::post('file/checkOut', [TransformerController::class, 'transform'])->name('file.checkOut');
        Route::post('file/bulkCheckIn', [TransformerController::class, 'transform'])->name('file.bulkCheckIn');
        Route::post('file/uploadFiles', [TransformerController::class, 'transform'])->name('file.uploadFiles');
        Route::delete('file/removeFiles', [TransformerController::class, 'transform'])->name('file.removeFiles');
        Route::get('file/readFile/{id}', [TransformerController::class, 'transform'])->name('file.readFile');
    });
});
