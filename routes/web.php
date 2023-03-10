<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Code\VerificationCodeController;

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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified','code_verified'])->name('dashboard');


Route::get('/verify/code', [VerificationCodeController::class, 'store'])->name('verify_code');

Route::get('/code', [VerificationCodeController::class, 'show'])->middleware('signed')->name('show_code');

Route::post('/validate/login/code', [VerificationCodeController::class, 'validate_code_login'])->name('last_code');

Route::middleware(['auth', 'code_verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
