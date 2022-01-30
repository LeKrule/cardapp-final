<?php

use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\CartasController;
use App\Http\Controllers\usuariosController;
use Illuminate\Http\Request;
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

Route::put('registrar', [usuariosController::class, 'registrar']);
Route::put('login', [usuariosController::class, 'login']);
Route::put('RecuperarPass', [usuariosController::class, 'RecuperarPass']);

Route::put('BuscarAnuncio', [AnuncioController::class, 'BuscarAnuncio']);

Route::middleware(["Logeado"])->group(function () {
    Route::put('vender', [AnuncioController::class, 'vender']);
    Route::put('buscar', [AnuncioController::class, 'buscar']);
});

Route::middleware(["UserAdmin"])->group(function () {
    Route::put('CrearCarta', [CartasController::class, 'CrearCarta']);
    Route::put('CrearColeccion', [CartasController::class, 'CrearColeccion']);
    Route::put('AñadirCarta', [CartasController::class, 'AñadirCarta']);
});
