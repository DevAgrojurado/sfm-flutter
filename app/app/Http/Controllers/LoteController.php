<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        
        // Si el usuario es evaluador, solo puede ver lotes de su finca
        if ($usuario->soloAccesoAFinca()) {
            return Lote::with('finca')
                ->where('idFinca', $usuario->idFinca)
                ->get();
        }
        
        // Si el usuario tiene acceso total (admin o coordinador), ve todos los lotes
        return Lote::with('finca')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para agregar
        if (!$usuario->puedeAgregar()) {
            return response()->json(['error' => 'No tiene permisos para agregar lotes'], 403);
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'idFinca' => 'required|exists:finca,id',
        ]);
        
        // Si el usuario es evaluador, solo puede crear lotes en su finca
        if ($usuario->soloAccesoAFinca() && $request->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para crear lotes en esta finca'], 403);
        }

        $lote = Lote::create($request->all());
        return response()->json($lote, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $usuario = $request->user();
        $lote = Lote::with('finca')->findOrFail($id);
        
        // Si el usuario es evaluador, solo puede ver lotes de su finca
        if ($usuario->soloAccesoAFinca() && $lote->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para ver este lote'], 403);
        }
        
        return $lote;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para editar
        if (!$usuario->puedeEditar()) {
            return response()->json(['error' => 'No tiene permisos para editar lotes'], 403);
        }
        
        $lote = Lote::findOrFail($id);
        
        // Si el usuario es evaluador, solo puede editar lotes de su finca
        if ($usuario->soloAccesoAFinca() && $lote->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para editar este lote'], 403);
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'idFinca' => 'required|exists:finca,id',
        ]);
        
        // Si está cambiando la finca y es evaluador, verificar que sea su finca
        if ($usuario->soloAccesoAFinca() && $request->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para asignar el lote a esta finca'], 403);
        }

        $lote->update($request->all());
        
        return response()->json($lote, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para eliminar
        if (!$usuario->puedeEliminar()) {
            return response()->json(['error' => 'No tiene permisos para eliminar lotes'], 403);
        }
        
        $lote = Lote::findOrFail($id);
        
        // Si el usuario es evaluador, solo puede eliminar lotes de su finca
        if ($usuario->soloAccesoAFinca() && $lote->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para eliminar este lote'], 403);
        }
        
        $lote->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Get lotes by finca id
     */
    public function getByFincaId(Request $request, string $fincaId)
    {
        $usuario = $request->user();
        
        // Si el usuario es evaluador, solo puede ver lotes de su finca
        if ($usuario->soloAccesoAFinca() && $fincaId != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para ver lotes de esta finca'], 403);
        }
        
        $lotes = Lote::with('finca')
            ->where('idFinca', $fincaId)
            ->get();
            
        return response()->json($lotes, 200);
    }
}
