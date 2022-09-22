<?php

use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\MagazinesController;
use App\Http\Middleware\EnforceJson;
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

Route::group([
    'prefix' => '/magazine',
    'middleware' => EnforceJson::class
], function () {
    Route::get('/list', [MagazinesController::class, 'magazines']);
    Route::post('/add', [MagazinesController::class, 'addMagazine']);
    Route::post('/update', [MagazinesController::class, 'updateMagazine']);
    Route::post('/delete', [MagazinesController::class, 'deleteMagazine']);
});

Route::group([
    'prefix' => '/author',
    'middleware' => EnforceJson::class
], function () {
    Route::get('/list', [AuthorsController::class, 'authors']);
    Route::post('/add', [AuthorsController::class, 'addAuthor']);
    Route::post('/update', [AuthorsController::class, 'updateAuthor']);
    Route::post('/delete', [AuthorsController::class, 'deleteAuthor']);
});
