<?php

namespace App\Modules\Inscriptos\Providers;

use App\Modules\Inscriptos\Http\Livewire\GrupoFamiliar;
use App\Modules\Inscriptos\Http\Livewire\Index;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class InscriptoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Views/', 'inscriptos');

        Livewire::component('inscriptos.index', Index::class);
        Livewire::component('inscriptos.grupo_familiar', GrupoFamiliar::class);
    }
}
