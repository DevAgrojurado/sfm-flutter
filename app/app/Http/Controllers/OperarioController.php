<?php

namespace App\Http\Controllers;

use App\Models\Operario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OperarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        
        // Si el usuario es evaluador, solo puede ver operarios de su finca
        if ($usuario->soloAccesoAFinca()) {
            return Operario::with(['cargo', 'area', 'finca'])
                ->where('fincaId', $usuario->idFinca)
                ->get();
        }
        
        // Si el usuario tiene acceso total, ve todos los operarios
        return Operario::with(['cargo', 'area', 'finca'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para agregar
        if (!$usuario->puedeAgregar()) {
            return response()->json(['error' => 'No tiene permisos para agregar operarios'], 403);
        }
        
        $request->validate([
            'codigo' => 'required|string|max:50|unique:operario,codigo',
            'nombre' => 'required|string|max:255',
            'cargoId' => 'required|exists:cargo,id',
            'areaId' => 'required|exists:area,id',
            'fincaId' => 'required|exists:finca,id',
            'activo' => 'boolean',
        ]);
        
        // Si el usuario es evaluador, solo puede crear operarios en su finca
        if ($usuario->soloAccesoAFinca() && $request->fincaId != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para asignar operarios a esta finca'], 403);
        }

        $operario = Operario::create($request->all());
        return response()->json($operario, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $usuario = $request->user();
        $operario = Operario::with(['cargo', 'area', 'finca'])->findOrFail($id);
        
        // Si el usuario es evaluador, solo puede ver operarios de su finca
        if ($usuario->soloAccesoAFinca() && $operario->fincaId != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para ver este operario'], 403);
        }
        
        return $operario;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para editar
        if (!$usuario->puedeEditar()) {
            return response()->json(['error' => 'No tiene permisos para editar operarios'], 403);
        }
        
        $operario = Operario::findOrFail($id);
        
        // Si el usuario es evaluador, solo puede editar operarios de su finca
        if ($usuario->soloAccesoAFinca() && $operario->fincaId != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para editar este operario'], 403);
        }
        
        $request->validate([
            'codigo' => 'required|string|max:50|unique:operario,codigo,' . $id,
            'nombre' => 'required|string|max:255',
            'cargoId' => 'required|exists:cargo,id',
            'areaId' => 'required|exists:area,id',
            'fincaId' => 'required|exists:finca,id',
            'activo' => 'boolean',
        ]);
        
        // Si está cambiando la finca y es evaluador, verificar que sea su finca
        if ($usuario->soloAccesoAFinca() && $request->fincaId != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para asignar el operario a esta finca'], 403);
        }

        $operario->update($request->all());
        
        return response()->json($operario, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para eliminar
        if (!$usuario->puedeEliminar()) {
            return response()->json(['error' => 'No tiene permisos para eliminar operarios'], 403);
        }
        
        $operario = Operario::findOrFail($id);
        
        // Si el usuario es evaluador, solo puede eliminar operarios de su finca
        if ($usuario->soloAccesoAFinca() && $operario->fincaId != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para eliminar este operario'], 403);
        }
        
        $operario->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
