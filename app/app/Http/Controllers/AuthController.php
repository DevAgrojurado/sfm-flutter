<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !password_verify($request->password, $usuario->clave)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        if (!$usuario->vigente) {
            return response()->json(['message' => 'Usuario no vigente'], 403);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;
        
        // Cargar relaciones del usuario
        $usuario->load(['cargo', 'area', 'finca']);
        
        // Agregar información sobre permisos
        $permisos = [
            'puede_agregar' => $usuario->puedeAgregar(),
            'puede_editar' => $usuario->puedeEditar(),
            'puede_eliminar' => $usuario->puedeEliminar(),
            'acceso_total' => $usuario->tieneAccesoTotal(),
            'rol_tipo' => $usuario->tipo_rol
        ];

        return response()->json([
            'token' => $token, 
            'usuario' => $usuario,
            'permisos' => $permisos
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}