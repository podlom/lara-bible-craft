<?php

use App\Http\Controllers\CitationStyleController;
use App\Http\Controllers\BibliographyController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;


Route::resource('citation-styles', CitationStyleController::class);
Route::resource('bibliographies', BibliographyController::class);
Route::resource('sources', SourceController::class);

Route::get('/', function () {
    return view('welcome');
});
