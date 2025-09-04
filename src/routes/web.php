<?php

use App\Http\Controllers\BibliographyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SourceController;
use App\Models\Bibliography;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    $bibliographies = Bibliography::where('user_id', $user->id)->get();

    return view('dashboard', compact('bibliographies'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/pagination', [ProfileController::class, 'updatePagination'])->name('profile.pagination.update');
});

Route::resource('sources', SourceController::class)->middleware('auth');
Route::resource('bibliographies', BibliographyController::class)->middleware('auth');

Route::get('/lang/{lang}', function ($lang) {
    session(['locale' => in_array($lang, ['en', 'uk']) ? $lang : 'en']);

    return back();
})->name('lang.switch');

require __DIR__.'/auth.php';
