<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PythonController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\StripeController;

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

Route::get('/run-python', [PythonController::class, 'runPython'])->name('runPython');
Route::post('/add-dns-record', [PythonController::class, 'addDnsRecord'])->name('addDnsRecord');
Route::get('/get-dns-record/{id}', [PythonController::class, 'getDnsRecordDetailsById']); 
Route::post('/update-dns-record', [PythonController::class, 'updateDnsRecord'])->name('updateDnsRecord');

Route::get('/previewLibrary', [LibraryController::class, 'index'])->name('index.library');
Route::post('/addFolders', [LibraryController::class, 'create'])->name('create.library');
Route::get('/get-folders', [LibraryController::class, 'getFolders'])->name('getFolders.library');
Route::get('/delete-folder', [LibraryController::class, 'destroy'])->name('deleteFolders.library');
Route::post('/upload-image', [LibraryController::class, 'uploadImage'])->name('uploadImage.library');
Route::get('/getImages/library', [LibraryController::class, 'getImages'])->name('getImages.library');
Route::get('/deleteImage', [LibraryController::class, 'deleteImage'])->name('deleteImage.library');
Route::post('/editFolder', [LibraryController::class, 'editFolder'])->name('editFolder.library');
// Chatbot
Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/start', [ChatbotController::class, 'start'])->name('chatbot.start');
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');

// Stripe Test
Route::get('/stripe', [StripeController::class, 'index'])->name('stripe.index');
Route::post('/stripe/intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.intent');
Route::get('/stripe/payment-methods', [StripeController::class, 'paymentMethods'])->name('stripe.methods');
Route::post('/stripe/setup-intent', [StripeController::class, 'createSetupIntent'])->name('stripe.setup-intent');
Route::delete('/stripe/payment-method/{paymentMethodId}', [StripeController::class, 'detachPaymentMethod'])->name('stripe.detach');
Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');