<?php

use App\Modules\Inscriptos\Http\Livewire\Index;

Route::middleware(['web'])->group(function () {
    Route::middleware('verified')->group(function () {
        Route::prefix('admin/inscriptos')->group(function () {
            Route::get('/', Index::class)->name('admin.inscriptos');
        });
    });
});
