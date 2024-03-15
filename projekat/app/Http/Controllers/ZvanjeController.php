<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreZvanjeRequest;
use App\Models\Zvanje;
use App\Services\ZvanjeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
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

    public function store(StoreZvanjeRequest $request)
    {
//        dd($request)

        $validated = $request->safe()->only([
            'naziv',
            'nivo'
        ]);


        $result = $this->zvanjeService->store($validated);

        if($result instanceof Validator && $result->fails()){
            $err_msgs = $result->errors();
            Log::error($err_msgs);
            return back()->withErrors($err_msgs);
        }


        return redirect(route('zvanje.index'));
    }

    public function edit(Zvanje $zvanje)
    {
        return view('zvanje.edit', ['zvanje' => $zvanje]);
    }

    public function update(Zvanje $zvanje, StoreZvanjeRequest $request) {
        $validated = $request->safe()->only([
            'naziv',
            'nivo'
        ]);

        $this->zvanjeService->update($zvanje, $validated);
        //$zvanje->update($validated);

        return redirect(route('zvanje.index'))->with('success', 'Zvanje uspešno izmenjeno');
    }

    public function destroy(Zvanje $zvanje) {
        $zvanje->delete();

        return redirect(route('zvanje.index'))->with('success', 'Zvanje uspešno obrisano');
    }


}
