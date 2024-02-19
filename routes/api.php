<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WordController;
use App\Http\Controllers\API\SecurityController;
use App\Http\Controllers\API\CategorieController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function(){
        return Auth::user();
    });
});

Route::post('/register', [SecurityController::class, 'register']);
Route::post('/login', [SecurityController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [SecurityController::class, 'logout']);

Route::prefix('/users')->group(function(){
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/edit/{user}', [UserController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('/categories')->group(function(){
    Route::get('/', [CategorieController::class, 'index']);
    Route::get('/{categorie}', [CategorieController::class, 'show']);
    Route::post('/', [CategorieController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/edit/{categorie}', [CategorieController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{categorie}', [CategorieController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('/words')->group(function(){
    Route::get('/', [WordController::class, 'index']);
    Route::get('/{word}', [WordController::class, 'show']);
    Route::post('/', [WordController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/edit/{word}', [WordController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{word}', [WordController::class, 'destroy'])->middleware('auth:sanctum');
});