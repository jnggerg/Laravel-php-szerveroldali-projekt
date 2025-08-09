<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'showDashboardStats'])->middleware(['auth', 'verified'])->name('dashboard');
#kifutok és állatok száma, teendők

Route::post('/dashboard/new-enclosure', [FormController::class, 'newEnclosure'])->middleware(['auth', 'verified'])->name('dashboard.new_enclosure');
Route::post('/dashboard/edit-enclosure', [FormController::class, 'editEnclosure'])->middleware(['auth', 'verified'])->name('dashboard.edit_enclosure');
Route::post('/dashboard/del-enclosure', [FormController::class, 'delEnclosure'])->middleware(['auth', 'verified'])->name('dashboard.del_enclosure');
Route::get('/dashboard/show-enclosure', [FormController::class, 'showEnclosure'])->middleware(['auth', 'verified'])->name('dashboard.show_enclosure');
Route::post('/enclosuredetails/{enclosure}/new-animal', [FormController::class, 'newAnimal'])->middleware(['auth', 'verified'])->name('enclosuredetails.new_animal');
Route::post('/enclosuredetails/{enclosure}/edit-animal/{animal}', [FormController::class, 'editAnimal'])->middleware(['auth', 'verified'])->name('enclosuredetails.edit_animal');
Route::post('/enclosuredetails/{enclosure}/del-animal/{animal}', [FormController::class, 'delAnimal'])->middleware(['auth', 'verified'])->name('enclosuredetails.del_animal');
Route::post('/dashboard/restore-animal/{animal}', [FormController::class, 'restoreAnimal'])->middleware(['auth', 'verified'])->name('dashboard.restore_animal');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
