<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ViewsHandler;
use \App\Http\Controllers\GetPostHandler;

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

/**
 * General routes for views
 */
Route::get('/', [ViewsHandler::class, 'login']);
Route::post('/do-login', [ViewsHandler::class, 'do_login']);
Route::get('/logout', [ViewsHandler::class, 'logout']);
Route::get('/{module}/{view}',[ViewsHandler::class, 'loadView'])->middleware('auth');
Route::get('/{module}/{view}/{id}',[ViewsHandler::class, 'loadView'])->middleware('auth');
/**
 * Get requests routes mainly json as response
 */
Route::get('/api/{func}',[GetPostHandler::class, 'get']);
Route::get('/api/{func}/{id}',[GetPostHandler::class, 'get']);
/**
 * Post requests handler routes mainly json as response
 */
Route::post('/post/{func}',[GetPostHandler::class, 'post']);
