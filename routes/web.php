<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('task-manager/index');
});

Route::get('/tasks', [ApiController::class, 'index'])->name('index');
Route::get('/addTasks', [ApiController::class, 'add'])->name('add');
Route::get('/editTasks', [ApiController::class, 'edit'])->name('edit');
Route::post('/storeTask', [ApiController::class, 'store'])->name('store');
Route::post('/updateTask', [ApiController::class, 'update'])->name('update');
Route::get('/deleteTask', [ApiController::class, 'destroy'])->name('delete');
Route::get('/previewTask', [ApiController::class, 'preview'])->name('preview');
Route::get('/weatherUpdate', [ApiController::class, 'weather'])->name('weather');