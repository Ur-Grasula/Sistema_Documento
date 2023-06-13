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


// ROTA DE LISTAGEM DE DOCUMENTO
Route::get('/Documento_Read', [Documento_Controller::class, 'View_Documento_Read'])->name('documento_read');

// ROTA DE DONWLOAD DE DOCUMENTO
Route::match(['get', 'post'], '/Documento_Download/{id}', [Documento_Controller::class, 'View_Documento_Download'])->name('documento_download');

// ROTA DE UPLOAD DE DOCUMENTO
Route::get('/Documento_Upload', [Documento_Controller::class, 'View_Documento_Upload'])->name('documento_upload');
Route::match(['get', 'post'], '/Documento_Upload_Validate', [Documento_Controller::class, 'View_Documento_Upload_Validate'])->name('documento_upload_validate');

// ROTA DE DELETE DE DOCUMENTO
Route::match(['get', 'post'], 'Documento_Delete/{id}', [Documento_Controller::class, 'View_Documento_Delete'])->name('documento_delete');

// ROTA DE UPDATE DE DOCUMENTO
Route::match(['get', 'post'], '/Documento_Update/{id}', [Documento_Controller::class, 'View_Documento_Update'])->name('documento_update');
Route::match(['get', 'post'], '/Documento_Update_validate/{id}', [Documento_Controller::class, 'View_Documento_Update_Validate'])->name('documento_update_validate');

// ROTA DE PESQUISA
// NOTA - ROTA NECESSARIO ?
Route::get('/Search', [Documento_Controller::class, 'Search'])->name('search');
