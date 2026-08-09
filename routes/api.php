<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Mobility\BusController;
use App\Http\Controllers\Mobility\LrtController;
use App\Http\Controllers\Mobility\MrtController;
use App\Http\Controllers\Mobility\TrainController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Service\ServiceController;
use App\Models\TrainStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//auth
Route::group([
    'prefix' => 'auth'
], function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/verify-email', [AuthController::class, 'verify_email']);
    Route::post('/resend', [AuthController::class, 'resend']);
});

Route::group([
    'prefix' => 'mobility',
    'middleware' => 'auth:sanctum'
], function() {

    //Train
    Route::group([
        'prefix' => 'train'
    ], function() {
        Route::get('/list-station', function() {
            return TrainStation::get();
        });
        Route::post('/create-train', [TrainController::class, 'createTrain']);
        Route::get('/list-train', [TrainController::class, 'listTrain']);
        Route::get('/detail-train/{id}', [TrainController::class, 'detailTrain']);
    });

    //Bus
    Route::group([
        'prefix' => 'bus'
    ], function() {
        Route::post('/create-bus', [BusController::class, 'createBus']);
        Route::get('/list-bus', [BusController::class, 'showBus']);
        Route::get('/list-corridor', [BusController::class, 'showRoute']);
    });

    //MRT
    Route::group([
        'prefix' => 'mrt'
    ], function() {
        Route::post('/create-mrt', [MrtController::class, 'createMrt']);
        Route::get('/list-mrt', [MrtController::class, 'showMrt']);
        Route::get('/list-station', [MrtController::class, 'showStation']);
    });

    //LRT
    Route::group([
        'prefix' => 'lrt'
    ], function() {
        Route::post('/create-lrt', [LrtController::class, 'createLrt']);
        Route::get('/list-lrt', [LrtController::class, 'showLrt']);
        Route::get('/detail-lrt/{id}', [LrtController::class, 'detailLrt']);
    });
});


Route::group([
    'prefix' => 'report',
    'middleware' => 'auth:sanctum'
], function() {
    Route::post('/', [ReportController::class, 'createReport']);
    Route::get('/user-report', [ReportController::class, 'getUserReport']);
    Route::get('/all-report', [ReportController::class, 'getAllReport']);
    Route::put('/{id}', [ReportController::class, 'markFinish']);
    Route::delete('/{id}', [ReportController::class, 'deleteReport']);
});

Route::group([
    'prefix' => 'service',
    'middleware' => 'auth:sanctum'
], function() {
    Route::post('/', [ServiceController::class, 'booking']);
    Route::get('/', [ServiceController::class, 'getAllService']);
    Route::get('/user-booking', [ServiceController::class, 'getUserBooking']);
    Route::get('/all-booking', [ServiceController::class, 'getAllBooking']);
    Route::put('/accept-booking/{id}', [ServiceController::class, 'accept']);
    Route::put('/reject-booking/{id}', [ServiceController::class, 'reject']);
});


Route::group([
    'prefix' => 'profile',
    'middleware' => 'auth:sanctum'
], function() {
    Route::put('/data-profile', [ProfileController::class, 'changeProfile']);
    Route::post('/photo-profile', [ProfileController::class, 'changePhoto']);
    Route::post('/delete-photo', [ProfileController::class, 'deletePhoto']);
    Route::get('/', [ProfileController::class, 'getProfile']);
});
