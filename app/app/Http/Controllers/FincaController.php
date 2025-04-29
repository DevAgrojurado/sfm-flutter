<?php

namespace App\Http\Controllers;

use App\Models\Finca;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FincaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        
        // Si el usuario es evaluador, solo puede ver su finca
        if ($usuario->soloAccesoAFinca()) {
            return Finca::where('id', $usuario->idFinca)->get();
        }
        
        // Si el usuario tiene acceso total (admin o coordinador), ve todas las fincas
        return Finca::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para agregar
        if (!$usuario->puedeAgregar()) {
            return response()->json(['error' => 'No tiene permisos para agregar fincas'], 403);
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $finca = Finca::create($request->all());
        return response()->json($finca, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $usuario = $request->user();
        $finca = Finca::findOrFail($id);
        
        // Si el usuario es evaluador, solo puede ver su finca
        if ($usuario->soloAccesoAFinca() && $finca->id != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para ver esta finca'], 403);
        }
        
        return $finca;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para editar
        if (!$usuario->puedeEditar()) {
            return response()->json(['error' => 'No tiene permisos para editar fincas'], 403);
        }
        
        $finca = Finca::findOrFail($id);
        
        // Si el usuario es evaluador, solo puede editar su propia finca
        if ($usuario->soloAccesoAFinca() && $finca->id != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para editar esta finca'], 403);
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $finca->update($request->all());
        
        return response()->json($finca, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para eliminar
        if (!$usuario->puedeEliminar()) {
            return response()->json(['error' => 'No tiene permisos para eliminar fincas'], 403);
        }
        
        $finca = Finca::findOrFail($id);
        
        // Si el usuario es evaluador, no debería poder eliminar fincas (aunque sea la suya)
        if ($usuario->soloAccesoAFinca()) {
            return response()->json(['error' => 'No tiene permisos para eliminar fincas'], 403);
        }
        
        $finca->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
