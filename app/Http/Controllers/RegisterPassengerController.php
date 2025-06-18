<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Passenger;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RegisterPassengerController extends Controller
{

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
    'nombre' => 'required|string|max:50',
    'apellidos' => 'required|string|max:50',
    'ine' => 'nullable|string|max:45',
    'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'ine_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'correo' => 'required|email|unique:passenger,correo',
    'telefono' => 'nullable|string|max:45',
    'contrasena' => 'required|min:6|confirmed',
    'genero_id_genero' => 'required|integer|exists:gender,id_genero',
    'idioma_id_idioma' => 'required|integer|exists:lenguage,id_idioma',
    'discapacidad' => 'nullable|string|max:45',
    'numerocuenta' => 'nullable|integer',
    'fechaexpiracion' => 'nullable|integer',
    'cvv' => 'nullable|integer',
]);


    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $fotoPath = null;
    if ($request->hasFile('foto')) {
        try {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('fotos', 'public');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar la imagen de perfil'], 500);
        }
    }

    $ineImagenPath = null;
    if ($request->hasFile('ine_imagen')) {
        try {
            $ineImagen = $request->file('ine_imagen');
            $ineImagenPath = $ineImagen->store('ines', 'public'); // Carpeta separada para ine
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar la imagen del INE'], 500);
        }
    }


    DB::beginTransaction();
    try {
       $passenger = new Passenger([
        'nombre' => $request->nombre,
        'apellidos' => $request->apellidos,
        'ine' => $request->ine,
        'foto' => $fotoPath,
        'ine_imagen' => $ineImagenPath,  // Guardamos ruta de la imagen del INE
        'correo' => $request->correo,
        'telefono' => $request->telefono,
        'contrasena' => bcrypt($request->contrasena),
        'genero_id_genero' => $request->genero_id_genero,
        'idioma_id_idioma' => $request->idioma_id_idioma,
        'discapacidad' => $request->discapacidad,
        'numerocuenta' => $request->numerocuenta,
        'fechaexpiracion' => $request->fechaexpiracion,
        'cvv' => $request->cvv,
    ]);

        $passenger->save();

        // Simulando token si no se usa tabla users
        $fakeUser = new \stdClass();
        $fakeUser->name = $passenger->nombre;
        $fakeUser->email = $passenger->correo;
        $tokenResource = [
            'access_token' => 'simulated-token',
            'token_type' => 'Bearer',
            'expires_at' => Carbon::now()->addMinutes(15)->toDateTimeString(),
            'token_id' => null,
            'name' => $fakeUser->name,
            'email' => $fakeUser->email,
        ];

        DB::commit();

        return response()->json(['token' => $tokenResource, 'user' => $passenger], 201);

    } catch (\Throwable $th) {
        DB::rollback();
        Log::error('Error in register method: '.$th->getMessage());
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json(['error' => 'No autorizado'], 406);
            }

            $user = Auth::user();

            if ($user->status != 'ACTIVE') {
                return response()->json(['error' => 'Usuario inactivo'], 403);
            }

            $tokens = $user->tokens;
            foreach ($tokens as $token) $token->revoke();
            $tokenResource = $this->generateToken($user);

            DB::commit();

            return response()->json([
                'success' => $tokenResource,


            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error in login method: '.$th->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function generateToken($user)
    {
        $tokenResult = $user->createToken('TokenPassenger');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addMinutes(15);
        $token->save();

        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at->toDateTimeString(),
            'token_id' => $token->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $tokens = $user->tokens;
            foreach ($tokens as $token) $token->revoke();
            return response()->json(['message' => 'Sesión cerrada correctamente.'], 200);
        }

        return response()->json(['message' => 'Usuario no autenticado.'], 401);
    }

    public function index()
    {
        $users = Passenger::all();
        return response()->json(['users' => $users], 200);
    }
}
