<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TaxiDriver;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TaxiDriverController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_taxista' => 'required|integer|unique:taxi-driver,id_taxista',
            'nombre' => 'required|string|max:100',
            'edad' => 'nullable|integer|min:18|max:100',
            'ine' => 'nullable|string|max:45',
            'permiso_taxi' => 'nullable|string|max:45',
            'lincencia' => 'nullable|string|max:45',
            'telefono' => 'nullable|string|max:45',
            'contrasena' => 'required|min:6',
            'contrasena_confirmation' => 'required|same:contrasena',
            'idioma_id_idioma' => 'required|integer|exists:lenguage,id_idioma',
            'foto_conductor' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'foto_taxi' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'numero_cuenta' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $fotoConductorPath = null;
        if ($request->hasFile('foto_conductor')) {
            try {
                $fotoConductor = $request->file('foto_conductor');
                $fotoConductorPath = $fotoConductor->store('conductores', 'public');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al cargar la foto del conductor'], 500);
            }
        }

        $fotoTaxiPath = null;
        if ($request->hasFile('foto_taxi')) {
            try {
                $fotoTaxi = $request->file('foto_taxi');
                $fotoTaxiPath = $fotoTaxi->store('taxis', 'public');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al cargar la foto del taxi'], 500);
            }
        }

        DB::beginTransaction();
        try {
            $taxiDriver = new TaxiDriver([
                'id_taxista' => $request->id_taxista,
                'nombre' => $request->nombre,
                'edad' => $request->edad,
                'ine' => $request->ine,
                'permiso_taxi' => $request->permiso_taxi,
                'lincencia' => $request->lincencia,
                'telefono' => $request->telefono,
                'contrasena' => bcrypt($request->contrasena),
                'idioma_id_idioma' => $request->idioma_id_idioma,
                'foto_conductor' => $fotoConductorPath,
                'foto_taxi' => $fotoTaxiPath,
                'numero_cuenta' => $request->numero_cuenta,
            ]);

            $taxiDriver->save();

            // Simular token
            $tokenResource = [
                'access_token' => 'simulated-token-' . $taxiDriver->id_taxista,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::now()->addMinutes(15)->toDateTimeString(),
                'token_id' => null,
                'name' => $taxiDriver->nombre,
                'id_taxista' => $taxiDriver->id_taxista,
            ];

            DB::commit();

            return response()->json(['token' => $tokenResource, 'taxi_driver' => $taxiDriver], 201);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in taxi driver register method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_taxista' => 'required|integer',
            'contrasena' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $taxiDriver = TaxiDriver::where('id_taxista', $request->id_taxista)->first();

            if (!$taxiDriver || !password_verify($request->contrasena, $taxiDriver->contrasena)) {
                return response()->json(['error' => 'Credenciales incorrectas'], 401);
            }

            $tokenResource = [
                'access_token' => 'simulated-token-' . $taxiDriver->id_taxista,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::now()->addMinutes(15)->toDateTimeString(),
                'token_id' => null,
                'name' => $taxiDriver->nombre,
                'id_taxista' => $taxiDriver->id_taxista,
            ];

            DB::commit();

            return response()->json([
                'success' => $tokenResource,
                'taxi_driver' => $taxiDriver
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in taxi driver login method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Sesión cerrada correctamente.'], 200);
    }

    public function index()
    {
        $taxiDrivers = TaxiDriver::with('idioma')->get();
        return response()->json(['taxi_drivers' => $taxiDrivers], 200);
    }

    public function show($id)
    {
        $taxiDriver = TaxiDriver::with(['idioma', 'vehicles'])->find($id);

        if (!$taxiDriver) {
            return response()->json(['error' => 'Taxista no encontrado'], 404);
        }

        return response()->json(['taxi_driver' => $taxiDriver], 200);
    }

    public function update(Request $request, $id)
    {
        $taxiDriver = TaxiDriver::find($id);

        if (!$taxiDriver) {
            return response()->json(['error' => 'Taxista no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:100',
            'edad' => 'sometimes|integer|min:18|max:100',
            'ine' => 'sometimes|string|max:45',
            'permiso_taxi' => 'sometimes|string|max:45',
            'lincencia' => 'sometimes|string|max:45',
            'telefono' => 'sometimes|string|max:45',
            'idioma_id_idioma' => 'sometimes|integer|exists:lenguage,id_idioma',
            'foto_conductor' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'foto_taxi' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'numero_cuenta' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only([
                'nombre', 'edad', 'ine', 'permiso_taxi', 'lincencia',
                'telefono', 'idioma_id_idioma', 'numero_cuenta'
            ]);

            if ($request->hasFile('foto_conductor')) {
                $fotoConductor = $request->file('foto_conductor');
                $data['foto_conductor'] = $fotoConductor->store('conductores', 'public');
            }

            if ($request->hasFile('foto_taxi')) {
                $fotoTaxi = $request->file('foto_taxi');
                $data['foto_taxi'] = $fotoTaxi->store('taxis', 'public');
            }

            $taxiDriver->update($data);

            DB::commit();

            return response()->json(['taxi_driver' => $taxiDriver], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in taxi driver update method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function destroy($id)
    {
        $taxiDriver = TaxiDriver::find($id);

        if (!$taxiDriver) {
            return response()->json(['error' => 'Taxista no encontrado'], 404);
        }

        DB::beginTransaction();
        try {
            $taxiDriver->delete();
            DB::commit();

            return response()->json(['message' => 'Taxista eliminado correctamente'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in taxi driver destroy method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // Métodos para gestión de vehículos
    public function assignVehicle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_taxista' => 'required|integer|exists:taxi-driver,id_taxista',
            'id_vehiculo' => 'required|integer|exists:vehicle,id_vehiculo',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $taxiDriver = TaxiDriver::find($request->id_taxista);
            $vehicle = Vehicle::find($request->id_vehiculo);

            $taxiDriver->vehicles()->attach($request->id_vehiculo, [
                'taxi-driver_idioma_id_idioma' => $taxiDriver->idioma_id_idioma
            ]);

            DB::commit();

            return response()->json(['message' => 'Vehículo asignado correctamente'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in assign vehicle method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function removeVehicle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_taxista' => 'required|integer|exists:taxi-driver,id_taxista',
            'id_vehiculo' => 'required|integer|exists:vehicle,id_vehiculo',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $taxiDriver = TaxiDriver::find($request->id_taxista);
            $taxiDriver->vehicles()->detach($request->id_vehiculo);

            DB::commit();

            return response()->json(['message' => 'Vehículo removido correctamente'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in remove vehicle method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}
