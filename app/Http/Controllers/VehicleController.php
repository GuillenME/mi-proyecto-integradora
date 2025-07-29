<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Vehicle;
use App\Models\TaxiDriver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('taxiDrivers')->get();
        return response()->json(['vehicles' => $vehicles], 200);
    }

    public function show($id)
    {
        $vehicle = Vehicle::with('taxiDrivers')->find($id);

        if (!$vehicle) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        return response()->json(['vehicle' => $vehicle], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_vehiculo' => 'required|integer|unique:vehicle,id_vehiculo',
            'placa' => 'nullable|string|max:45',
            'num_taxi' => 'nullable|integer',
            'modelo' => 'nullable|string|max:45',
            'marca' => 'nullable|string|max:45',
            'numero_serie' => 'nullable|string|max:45',
            'anio' => 'nullable|string|max:45',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $vehicle = new Vehicle([
                'id_vehiculo' => $request->id_vehiculo,
                'placa' => $request->placa,
                'num_taxi' => $request->num_taxi,
                'modelo' => $request->modelo,
                'marca' => $request->marca,
                'numero_serie' => $request->numero_serie,
                'anio' => $request->anio,
            ]);

            $vehicle->save();

            DB::commit();

            return response()->json(['vehicle' => $vehicle], 201);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in vehicle store method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'placa' => 'sometimes|string|max:45',
            'num_taxi' => 'sometimes|integer',
            'modelo' => 'sometimes|string|max:45',
            'marca' => 'sometimes|string|max:45',
            'numero_serie' => 'sometimes|string|max:45',
            'anio' => 'sometimes|string|max:45',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only([
                'placa', 'num_taxi', 'modelo', 'marca', 'numero_serie', 'anio'
            ]);

            $vehicle->update($data);

            DB::commit();

            return response()->json(['vehicle' => $vehicle], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in vehicle update method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        DB::beginTransaction();
        try {
            $vehicle->delete();
            DB::commit();

            return response()->json(['message' => 'Vehículo eliminado correctamente'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in vehicle destroy method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function getAvailableVehicles()
    {
        $availableVehicles = Vehicle::whereDoesntHave('taxiDrivers')->get();
        return response()->json(['available_vehicles' => $availableVehicles], 200);
    }

    public function getAssignedVehicles()
    {
        $assignedVehicles = Vehicle::whereHas('taxiDrivers')->with('taxiDrivers')->get();
        return response()->json(['assigned_vehicles' => $assignedVehicles], 200);
    }
}
