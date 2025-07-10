<?php

use App\Http\Controllers\EdificioController;
use App\Http\Controllers\MigraDataController;
use App\Http\Controllers\PreguntasFrecuentesController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\VisitumController;
use App\Http\Controllers\GraficasController;
use App\Http\Controllers\InfoVisitasController;
use App\Http\Controllers\EstudiantesController;
// use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\GeneracionDocumentosPDFController;
use App\Http\Controllers\InfoEstudiantesController;
// use App\Http\Controllers\EntidadEspecialController;


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

    Route::get('ValidaServicio', [MigraDataController::class, 'ValidaServicio']);
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
    Route::post('graficas', [GraficasController::class, 'graficas']);
    Route::post('handleReport', [InfoVisitasController::class, 'handleReport']);
    Route::post('migraData', [MigraDataController::class, 'migraData']);
    Route::post('Estudiante', [EstudiantesController::class, 'Estudiante']);
    Route::post('PersonalIndex', [PersonalController::class, 'PersonalIndex']);


    Route::prefix('catalogo/{catalogName}')->group(function () {
        Route::get('/', [CatalogoController::class, 'index'])->middleware('cache.headers:public;max_age=3600;etag');
        Route::post('/', [CatalogoController::class, 'store']);
        Route::get('/{id}', [CatalogoController::class, 'show']);
        Route::put('/{id}', [CatalogoController::class, 'update']);
        Route::delete('/{id}', [CatalogoController::class, 'destroy']);
    });

    Route::get('makeQrEstudiante', [GeneracionDocumentosPDFController::class, 'makeQrEstudiante']);
    Route::post('ReporteGeneralEstudiantes', [InfoEstudiantesController::class, 'ReporteGeneralEstudiantes']);
    // Route::get('verificarEntidadEspecial', [EntidadEspecialController::class, 'verificarEntidadEspecial']);


});
