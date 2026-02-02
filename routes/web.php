<?php

use App\Http\Controllers\SecureFileController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas protegidas para descarga de archivos
 * Todas requieren autenticación
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/secure/avatar/{filename}', [SecureFileController::class, 'downloadAvatar'])->name('secure.avatar');
    Route::get('/secure/document/{id}', [SecureFileController::class, 'downloadDocument'])->name('secure.document');
    Route::get('/secure/document-image/{id}', [SecureFileController::class, 'downloadDocumentImage'])->name('secure.document.image');
    Route::get('/secure/feedback/{id}', [SecureFileController::class, 'downloadFeedback'])->name('secure.feedback');
});
