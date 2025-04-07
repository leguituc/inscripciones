<?php

use App\Modules\Inscriptos\Http\Livewire\GrupoFamiliar;
use App\Modules\Inscriptos\Http\Livewire\Index;

Route::middleware(['web'])->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::prefix('admin/inscriptos')->group(function () {
            Route::get('/', Index::class)->name('admin.inscriptos');
            Route::get('grupo_familiar/{hash_inscripto_id}', GrupoFamiliar::class)->name('admin.inscriptos.grupo_familiar');
        });
    });
});
