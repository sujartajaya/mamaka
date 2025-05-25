<?php

namespace App\Http\Controllers;

use App\Models\Radcheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RadcheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validator = Validator::make($request->all(), [
                'username' => ['required'],
                'attribute' => ['required'],
                'op' => ['required'],
                'value' => ['required'],
        ]);
        $data = [];
        if ($validator) 
        {
            if ($validator->fails())
            {
                $data['error'] = true;
                $data['msg'] = $validator->messages();
                return response()->json($data,200);
            } else {
                $data['error'] = false;
                $guest = Radcheck::create($validator);
                $data['msg'] = $guest;
                return response()->json($data,201);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Radcheck $radcheck)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Radcheck $radcheck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Radcheck $radcheck)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Radcheck $radcheck)
    {
        //
    }
}
