<?php

namespace App\Modules\Inscriptos\Http\Livewire;

use App\Models\DatoPersonal;
use App\Models\Pariente;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

class GrupoFamiliar extends Component
{
    public $inscripto;

    public function mount($hash_inscripto_id)
    {
        $this->inscripto = DatoPersonal::findByHash($hash_inscripto_id);
    }

    public function render()
    {
        $parientes = $this->obtenerParientes();
        return view('inscriptos::grupo-familiar', ['parientes' => $parientes]);
    }

    private function obtenerParientes(): LengthAwarePaginator
    {
        $query = Pariente::query();
        $query->where('titular_id', $this->inscripto->id);
        return $query->paginate(20);
    }

    public function volver()
    {
        $this->redirectRoute('admin.inscriptos', navigate: true);
    }
}
