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
            $katedreDTO[] = tap(new KatedraDTO)->fromModel($katedra);
        }
        return view('katedra.index', ['katedre' => $katedreDTO]);
    }

    public function create()
    {
        // TODO dodati pretragu paginaciju itd
        $zaposleni = Zaposleni::all();
        return view('katedra.create', ['method' => 'post', 'zaposleni' => $zaposleni, 'katedra'=>null]);
    }

    public function store(StoreKatedraRequest $request)
    {
        //dd($request);
        /*$validated = $request->safe()->only([
            'naziv',
            'nivo'
        ]);*/


        $result = $this->katedraService->store($request);

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
        $katedra = tap(new KatedraDTO())->fromModel(Katedra::find($katedra_id));
        return view('katedra.create', ['method' => 'put', 'zaposleni' => $zaposleni, 'katedra' => $katedra]);
    }

    public function update(int $katedra_id, StoreKatedraRequest $request)
    {
        dd($request);
    }
}
