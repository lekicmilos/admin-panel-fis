<?php

namespace App\Http\Controllers;

use App\DTO\KatedraDTO;
use App\Http\Requests\StoreKatedraRequest;
use App\Models\Katedra;
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
        $katedre = Katedra::where('aktivna', 1)->get();

        $katedreDTO = [];
        foreach ($katedre as $katedra) {
            $katedreDTO[] = $this->katedraService->toDTO($katedra);
        }
        return view('katedra.index', ['katedre' => $katedreDTO]);
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

        /*if($result instanceof Validator && $result->fails()){
            $err_msgs = $result->errors();
            Log::error($err_msgs);
            return back()->withErrors($err_msgs);
        }*/
        return redirect(route('katedra.index'));
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
        // todo dodaj find or fail i prosledi
        $katedraDTO = $request->toDTO($katedra_id);

        $result = $this->katedraService->upsert($katedraDTO);
        return redirect(route('katedra.index'));
    }
}
