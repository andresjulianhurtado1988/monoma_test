<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Controllers\CandidateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// ruta del login

Route::post('api/login', [LoginController::class, 'login'])->name('login');

// rutas de controller usuario

Route::post('api/user/register', [UserController::class, 'register'])->name('register')->middleware([ApiAuthMiddleware::class]);

// rutas de controller candidato

Route::post('api/candidate/candidateRegister', [CandidateController::class, 'candidateRegister'])->name('candidateRegister')->middleware([ApiAuthMiddleware::class]);
Route::get('api/candidate/showAllCandidates', [CandidateController::class, 'showAllCandidates'])->name('showAllCandidates')->middleware([ApiAuthMiddleware::class]);
Route::get('api/candidate/showCandidate/{id}', [CandidateController::class, 'showCandidate'])->name('showCandidate')->middleware([ApiAuthMiddleware::class]);