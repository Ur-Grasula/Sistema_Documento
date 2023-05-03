<?php

use App\Http\Controllers\Documento_Controller;
use Illuminate\Support\Facades\Route;

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

// ROTA DA HOME PAGE
Route::get('/', [Documento_Controller::class, 'Index'])->name('home');


// ROTA DE LISTAGEM
Route::get('/Listar', [Documento_Controller::class, 'Listar'])->name('listar');

// ROTA DE DONWLOAD
Route::match(['get', 'post'], '/Download/{id}', [Documento_Controller::class, 'Download'])->name('download');

// ROTA DE UPLOAD
Route::get('/Upload', [Documento_Controller::class, 'Upload'])->name('upload');
Route::match(['get', 'post'], '/Upload_Submit', [Documento_Controller::class, 'Upload_Submit_Validate'])->name('upload_submit');

// ROTA DE DELETE
Route::match(['get', 'post'], 'Delete/{id}', [Documento_Controller::class, 'Delete'])->name('delete');

// ROTA AUTO UPDATE
Route::match(['get', 'post'], '/Update/{id}', [Documento_Controller::class, 'Update'])->name('update');
Route::match(['get', 'post'], '/Update_Submit/{id}', [Documento_Controller::class, 'Update_Submit_Validate'])->name('update_submit');

// ROTA DE PESQUISA
Route::get('/Search', [Documento_Controller::class, 'Search'])->name('search');
