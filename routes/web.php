<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;

/**
 * HALAMAN WEB (SEO, crawlable)
 * → gunakan nama rute: certifications.page & certifications.show
 */
Route::name('certifications.')->group(function () {
    Route::get('/', [ServiceController::class, 'page'])->name('page');
    Route::get('/sertifikasi/{service:slug}', [ServiceController::class, 'show'])->name('show');
});

/**
 * API untuk front-end (JSON)
 * (biarkan nama rutenya services.* untuk API)
 */
Route::prefix('api')->group(function () {
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/facets', [ServiceController::class, 'facets'])->name('services.facets');
    Route::get('/services/{service:slug}', [ServiceController::class, 'showJson'])->name('services.show.json');

    // (opsional) JSON mentah untuk debug/dev
    Route::get('/services.json', function () {
        $p = storage_path('app/services.json');
        abort_unless(is_file($p), 404);
        return response()->file($p, ['Content-Type' => 'application/json; charset=utf-8']);
    });
});
