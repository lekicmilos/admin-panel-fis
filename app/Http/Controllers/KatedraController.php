<?php

namespace App\Http\Controllers;

use App\DTO\KatedraDTO;
use App\Http\Requests\StoreKatedraRequest;
use App\Models\Katedra;
use App\Models\Pozicija;
use App\Models\Zaposleni;
use App\Services\KatedraService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

class KatedraController extends Controller
{
    protected $katedraService;

    /**
     * @param $katedraService
     */
    public function __construct(KatedraService $katedraService)
    {
        $this->katedraService = $katedraService;
    }


    public function index()
    {
        $katedre = Katedra::with('pozicija')->where('aktivna', 1)->get();

        return view('katedra.index', ['katedre' => $katedre]);
    }

    public function create()
    {
        $zaposleni = Zaposleni::all();
        return view('katedra.create', ['method' => 'post', 'zaposleni' => $zaposleni, 'katedra' => null]);
    }

    public function store(StoreKatedraRequest $request)
    {
        $katedraDTO = $request->toDTO();
        $result = $this->katedraService->upsert($katedraDTO);
        return redirect(route('katedra.index'))->with('success', ''.$result->naziv_katedre.' uspešno dodata.');
    }

    public function edit(int $katedra_id)
    {
        $zaposleni = Zaposleni::all();
        $katedra = Katedra::findOrFail($katedra_id);
        $katedraDTO = $this->katedraService->toDTO($katedra);
        return view('katedra.create', ['method' => 'put', 'zaposleni' => $zaposleni, 'katedra' => $katedraDTO]);
    }

    public function update(int $katedra_id, StoreKatedraRequest $request)
    {
        $katedraDTO = $request->toDTO($katedra_id);
        $result = $this->katedraService->upsert($katedraDTO);
        return redirect(route('katedra.index'))->with('success', ''.$result->naziv_katedre.' uspešno izmenjena.');
    }

    public function delete(int $katedra_id)
    {
        $katedra = Katedra::findOrFail($katedra_id);
        $katedra->update(['aktivna' => 0]);
        return redirect(route('katedra.index'))->with('success', 'Katedra '.$katedra->naziv_katedre.' uspešno obrisana.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $katedre = Katedra::where([
            ['aktivna', 1],
            ['naziv_katedre', 'REGEXP', $search]
        ])->get();
        return view('katedra.index', ['katedre' => $katedre]);
    }
}
