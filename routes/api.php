<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/userinfo', [AuthController::class, 'userinfo'])->middleware('auth:sanctum'); //no disponible no autenticados
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); //no disponible no autenticados

Route::get('/categories', [CategoriaController::class, 'index']); //Todas las categorías
Route::get('/category/{id_categoria}', [CategoriaController::class, 'show']); //Una categoría
Route::post('/create-category', [CategoriaController::class, 'store']); //Registra una categoría
Route::patch('/update-category/{id_categoria}', [CategoriaController::class, 'update']); //Actualiza (solo lo necesario)
Route::put('/update-category/{id_categoria}', [CategoriaController::class, 'update']); //Actualiza (todo)

Route::get('/providers', [ProveedorController::class, 'index']); //Todos los proveedores
Route::get('/provider/{id_proveedor}', [ProveedorController::class, 'show']); //Un proveedor
Route::post('/create-provider', [ProveedorController::class, 'store']); //Registra un proveedor
Route::patch('/update-provider/{id_proveedor}', [ProveedorController::class, 'update']); //Actualiza (solo lo necesario)
Route::put('/update-provider/{id_proveedor}', [ProveedorController::class, 'update']); //Actualiza (todo)