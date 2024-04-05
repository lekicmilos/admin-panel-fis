<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKatedraRequest;
use App\Models\Katedra;
use App\Models\Zaposleni;
use App\Services\KatedraService;
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
        $katedre = Katedra::all();
        return view('katedra.index', ['katedre' => $katedre]);
    }

    public function create()
    {
        // TODO dodati pretragu paginaciju itd
        $zaposleni = Zaposleni::all();
        return view('katedra.create', ['method' => 'post', 'zaposleni' => $zaposleni]);
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
    }
}
