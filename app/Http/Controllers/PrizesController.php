<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;



class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {

        $prizes = Prize::all();
        $totalProbability = $prizes->sum('probability');
        $actualDistribution = [];
        $results = [];

        // if ($totalProbability != 100) {
        //     return back()->withErrors(['probability' => 'Sum of all prizes probability must be 100%. Currently it\'s '.$totalProbability.'%. You have yet to add '.(100 - $totalProbability).'% to the prize.']);
        // }

        return view('prizes.index', ['prizes' => $prizes, 'totalProbability' => $totalProbability, 'actualDistribution' => $actualDistribution,  'results' => $results]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $totalProbability = Prize::sum('probability');
        return view('prizes.create', ['totalProbability' => $totalProbability]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'probability' => 'required|numeric|min:0|max:100',
        ]);
        
      
        $totalProbability = Prize::sum('probability');
        $mainProbability = '100';
        $tatalvalue = abs($totalProbability - $mainProbability);

        if ($totalProbability + $request->probability > 100) {
            return back()->withErrors(['probability' => 'Total probability exceeds 100%. only Reming this value '.$tatalvalue.'']);
        }
        
        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        $totalProbability = Prize::sum('probability');
        
        if ($totalProbability != 100) {
            return redirect()->route('prizes.create')->withErrors(['probability' => 'Sum of all prizes probability must be 100%. Currently it\'s '.$totalProbability.'%. You have yet to add '.(100 - $totalProbability).'% to the prize.']);
        }

        return to_route('prizes.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        $totalProbability = Prize::sum('probability');
        return view('prizes.edit', ['prize' => $prize, 'totalProbability' => $totalProbability]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $prize = Prize::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'probability' => 'required|numeric|min:0|max:100',
        ]);

        $totalProbability = Prize::where('id', '!=', $prize->id)->sum('probability');

        if ($totalProbability + $request->probability > 100) {
            return back()->withErrors(['probability' => 'Total probability exceeds 100%.']);
        }

        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        $totalProbability = Prize::sum('probability');
        if ($totalProbability != 100) {
            return redirect()->route('prizes.edit', $prize)->withErrors(['probability' => 'Sum of all prizes probability must be 100%. Currently it\'s '.$totalProbability.'%. You have yet to add '.(100 - $totalProbability).'% to the prize.']);
        }

        return to_route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


    public function simulate(Request $request)
    {
    
        $request->validate([
            'number_of_prizes' => 'required|integer',
        ]);

        $prizes = Prize::all();
        $totalProbability = $prizes->sum('probability');
    
        $numPrizes = $request->input('number_of_prizes');
       
        $results = [];
        foreach ($prizes as $prize) {
            $results[$prize->title] = 0;
        }
        

        for ($i = 0; $i < $numPrizes; $i++) {
            $rand = rand(0, 100);
            $sum = 0;
            foreach ($prizes as $prize) {
                $sum += $prize->probability;
                if ($rand <= $sum) {
                    $results[$prize->title]++;
                    break;
                }
            }
        }
    
        return view('prizes.index', compact('results', 'numPrizes', 'totalProbability','prizes'));
    }

    public function reset()
    {
        return to_route('prizes.index');
    }
}
