<?php

namespace App\Http\Controllers;

use App\Models\Katedra;
use App\Models\Zaposleni;
use Illuminate\Http\Request;

class KatedraController extends Controller
{
//    protected $katedraService;
//
//    /**
//     * @param $katedraService
//     */
//    public function __construct(KatedraService $katedraService)
//    {
//        $this->katedraService = $katedraService;
//    }
//
//
//    public function index()
//    {
//        $katedre = Katedra::all();
//        return view('katedra.index', ['katedre' => $katedre]);
//    }

    public function create()
    {
        // TODO dodati pretragu paginaciju itd
        $zaposleni = Zaposleni::all();
        return view('katedra.create', ['zaposleni' => $zaposleni]);
    }

    public function store(Request $request)
    {
        dd($request);
    }
}
