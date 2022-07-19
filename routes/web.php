<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ViewsHandler;

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

Route::get('/', [ViewsHandler::class, 'login']);
Route::get('/{module}/{view}',[ViewsHandler::class, 'loadView'])->middleware('auth');
Route::get('/{module}/{view}/{id}',[ViewsHandler::class, 'loadView'])->middleware('auth');
