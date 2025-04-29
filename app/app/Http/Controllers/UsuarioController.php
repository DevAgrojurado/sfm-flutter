<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        $query = Usuario::with(['cargo', 'area', 'finca']);
        
        // Si el usuario es evaluador, solo puede ver usuarios de su finca
        if ($usuario->soloAccesoAFinca()) {
            $query->where('idFinca', $usuario->idFinca);
        }
        
        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:usuario,codigo',
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:usuario,cedula',
            'email' => 'required|email|max:255|unique:usuario,email',
            'clave' => 'required|string|min:6',
            'idCargo' => 'required|exists:cargo,id',
            'idArea' => 'required|exists:area,id',
            'idFinca' => 'required|exists:finca,id',
            'rol' => 'required|string|max:50',
            'tipo_rol' => 'required|in:administrador,coordinador,evaluador,operario',
            'vigente' => 'boolean',
        ]);

        $data = $request->all();
        $data['clave'] = Hash::make($request->clave);

        $usuario = Usuario::create($data);
        return response()->json($usuario, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Usuario::with(['cargo', 'area', 'finca'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'codigo' => 'required|string|max:50|unique:usuario,codigo,' . $id,
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:usuario,cedula,' . $id,
            'email' => 'required|email|max:255|unique:usuario,email,' . $id,
            'idCargo' => 'required|exists:cargo,id',
            'idArea' => 'required|exists:area,id',
            'idFinca' => 'required|exists:finca,id',
            'rol' => 'required|string|max:50',
            'tipo_rol' => 'required|in:administrador,coordinador,evaluador,operario',
            'vigente' => 'boolean',
        ]);

        $data = $request->all();
        
        // Solo actualiza la contraseña si se proporciona una nueva
        if ($request->has('clave') && !empty($request->clave)) {
            $request->validate([
                'clave' => 'string|min:6',
            ]);
            $data['clave'] = Hash::make($request->clave);
        } else {
            unset($data['clave']);
        }

        $usuario->update($data);
        
        return response()->json($usuario, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Muestra el perfil del usuario autenticado.
     */
    public function perfil(Request $request)
    {
        $usuario = $request->user();
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
            'usuario' => $usuario,
            'permisos' => $permisos
        ]);
    }
}
