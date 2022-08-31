<?php

use App\Http\Controllers\API\Admin\BookController;
use App\Http\Controllers\API\Admin\EquipmentController;
use App\Http\Controllers\API\Admin\RentalStatsController;
use App\Http\Controllers\API\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('rentals/stats', RentalStatsController::class)->name('rentals.stats');

    Route::apiResources([
        'users' => UserController::class,
        'books' => BookController::class,
        'equipments' => EquipmentController::class,
    ]);
});
