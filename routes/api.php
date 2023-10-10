<?php

use App\Http\Controllers\EdificioController;
use App\Http\Controllers\MigraDataController;
use App\Http\Controllers\PreguntasFrecuentesController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\VisitumController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::group([
    'prefix' => 'Api_SICA',
], function () {

    Route::post('ValidaServicio', [MigraDataController::class, 'ValidaServicio']);
    Route::post('SelectIndex', [SelectController::class, 'SelectIndex']);
    Route::post('visita_index', [VisitumController::class, 'visita_index']);
    Route::post('bitacora', [VisitumController::class, 'bitacora']);
    Route::post('Edificio_index', [EdificioController::class, 'Edificio_index']);
    Route::post('AdminAyudas', [PreguntasFrecuentesController::class, 'AdminAyudas']);
    Route::post('AdminVideoTutoriales', [PreguntasFrecuentesController::class, 'AdminVideoTutoriales']);
    Route::post('AdminPreguntasFrecuentes', [PreguntasFrecuentesController::class, 'AdminPreguntasFrecuentes']);
    Route::post('AdminGuiaRapida', [PreguntasFrecuentesController::class, 'AdminGuiaRapida']);
    Route::post('obtenerguias', [PreguntasFrecuentesController::class, 'obtenerguias']);
    Route::post('obtenerDoc', [PreguntasFrecuentesController::class, 'obtenerDoc']);

});
