<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'success' => true,
            'cars' => Car::where('user_id', Auth::id())->where('isDeleted', false)->orderBy('id', 'desc')->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'color' => 'required',
            'licensePlate' => 'required'
        ]);

        $user = Auth::user();

        $car = new Car;
        $car->brand = $request->brand;
        $car->color = $request->color;
        $car->model = $request->model;
        $car->licensePlate = $request->licensePlate;
        $car->user_id = $user->id;
        $car->save();

        if ($user->car_id == '') {
            $user->car_id = $car->id;
            $user->save();
        }

        return response([
            'success' => true,
            'car' => $car
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->car_id == $id ) {
            return response([
                'success' => true,
                'error' => 'Selecione outro carro como principal antes de apagar.'
            ], 500);
        }
        if (Car::where('id', $id)->where('user_id', $user->id)->get()->count() > 0) {
            Car::where('id', $id)->update([
                'isDeleted' => true
            ]);
            
            return response([
                'success' => true,
                'message' => 'carro deletado com sucesso'
            ], 200);
        } else {
            return response([
                'success' => true,
                'error' => 'error ao apagar o carro'
            ], 500);
        }
    }
}
