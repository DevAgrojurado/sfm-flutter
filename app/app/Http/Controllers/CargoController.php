<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        
        // Si el usuario es evaluador, solo puede ver cargos relacionados con su finca
        if ($usuario->soloAccesoAFinca()) {
            // Para cargos, mostramos todos porque son datos básicos
            // pero podrías implementar una restricción si fuera necesario
        }
        
        return Cargo::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar que el usuario tiene permisos para agregar
        if (!$request->user()->puedeAgregar()) {
            return response()->json(['error' => 'No tiene permisos para agregar cargos'], 403);
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $cargo = Cargo::create($request->all());
        return response()->json($cargo, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $usuario = $request->user();
        $cargo = Cargo::findOrFail($id);
        
        // Si el usuario es evaluador, verifica si tiene acceso a este cargo
        if ($usuario->soloAccesoAFinca()) {
            // Para cargos, permitimos el acceso a los datos básicos
            // pero podrías implementar una restricción si fuera necesario
        }
        
        return $cargo;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Verificar que el usuario tiene permisos para editar
        if (!$request->user()->puedeEditar()) {
            return response()->json(['error' => 'No tiene permisos para editar cargos'], 403);
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $cargo = Cargo::findOrFail($id);
        $cargo->update($request->all());
        
        return response()->json($cargo, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Verificar que el usuario tiene permisos para eliminar
        if (!$request->user()->puedeEliminar()) {
            return response()->json(['error' => 'No tiene permisos para eliminar cargos'], 403);
        }
        
        $cargo = Cargo::findOrFail($id);
        $cargo->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
