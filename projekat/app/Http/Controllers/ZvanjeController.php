<?php

namespace App\Http\Controllers;

use App\Models\Zvanje;
use App\Services\ZvanjeService;
use Illuminate\Http\Request;
use Mockery\Exception;

class ZvanjeController extends Controller
{
    protected $zvanjeService;

    /**
     * @param $zvanjeService
     */
    public function __construct(ZvanjeService $zvanjeService)
    {
        $this->zvanjeService = $zvanjeService;
    }


    public function index()
    {
        $zvanja = Zvanje::all();
        return view('zvanje.index', ['zvanja' => $zvanja]);
    }

    public function create()
    {
        return view('zvanje.create');
    }

    public function store(Request $request)
    {
//        dd($request);
        $data = $request->only([
            'naziv',
            'nivo'
        ]);

        try {
            $this->zvanjeService->store($data);
        } catch (Exception $e) {
            //TODO error handling
        }

        return redirect(route('zvanje.index'));
    }

}
