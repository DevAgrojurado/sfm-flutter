<?php

namespace App\Http\Controllers;

use App\Models\EvaluacionGeneral;
use Illuminate\Http\Request;

class EvaluacionGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        $query = EvaluacionGeneral::with(['evaluador', 'polinizador', 'lote.finca']);
        
        // Filtrar según el tipo de rol
        if (!$usuario->tieneAccesoTotal()) {
            // Si es evaluador, solo ve las evaluaciones de su finca
            if ($usuario->soloAccesoAFinca()) {
                $query->whereHas('lote', function($q) use ($usuario) {
                    $q->where('idFinca', $usuario->idFinca);
                });
            } else {
                // Si es operario, solo ve sus propias evaluaciones
                $query->where(function($q) use ($usuario) {
                    $q->where('idevaluadorev', $usuario->id)
                      ->orWhere('idpolinizadorev', $usuario->id);
                });
            }
        }
        
        $evaluaciones = $query->get();
        return response()->json($evaluaciones, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para agregar
        if (!$usuario->puedeAgregar()) {
            return response()->json(['error' => 'No tiene permisos para agregar evaluaciones'], 403);
        }
        
        // Validar los datos de entrada
        $validated = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
            'semana' => 'required|integer|min:1|max:52',
            'idevaluadorev' => 'required|exists:usuario,id',
            'idpolinizadorev' => 'required|exists:operario,id',
            'idloteev' => 'required|exists:lote,id',
            'fotopath' => 'nullable|string|max:255',
            'firmapath' => 'nullable|string|max:255',
            'timestamp' => 'nullable|date',
        ]);
        
        // Si el usuario es evaluador, verificar que el lote pertenezca a su finca
        if ($usuario->soloAccesoAFinca()) {
            $loteId = $validated['idloteev'];
            $lote = \App\Models\Lote::find($loteId);
            
            if (!$lote || $lote->idFinca != $usuario->idFinca) {
                return response()->json(['error' => 'No tiene permisos para crear evaluaciones en esta finca'], 403);
            }
        }

        // Crear la evaluación general
        $evaluacion = EvaluacionGeneral::create($validated);
        
        // Cargar relaciones para la respuesta
        $evaluacion->load(['evaluador', 'polinizador', 'lote']);
        
        return response()->json($evaluacion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $usuario = $request->user();
        
        $evaluacion = EvaluacionGeneral::with(['evaluador', 'polinizador', 'lote.finca'])->find($id);
        if (!$evaluacion) {
            return response()->json(['error' => 'Evaluación general no encontrada'], 404);
        }
        
        // Verificar permisos según el tipo de rol
        if (!$usuario->tieneAccesoTotal()) {
            if ($usuario->soloAccesoAFinca()) {
                // Si es evaluador, verificar que la evaluación pertenezca a su finca
                if ($evaluacion->lote->idFinca != $usuario->idFinca) {
                    return response()->json(['error' => 'No tiene permisos para ver esta evaluación'], 403);
                }
            } else {
                // Si es operario, verificar que participe en la evaluación
                if ($evaluacion->idevaluadorev != $usuario->id && 
                    $evaluacion->idpolinizadorev != $usuario->id) {
                    return response()->json(['error' => 'No tiene permisos para ver esta evaluación'], 403);
                }
            }
        }
        
        return response()->json($evaluacion, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para editar
        if (!$usuario->puedeEditar()) {
            return response()->json(['error' => 'No tiene permisos para editar evaluaciones'], 403);
        }
        
        $evaluacion = EvaluacionGeneral::with('lote')->find($id);
        if (!$evaluacion) {
            return response()->json(['error' => 'Evaluación general no encontrada'], 404);
        }
        
        // Si el usuario es evaluador, verificar que la evaluación pertenezca a su finca
        if ($usuario->soloAccesoAFinca() && $evaluacion->lote->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para editar esta evaluación'], 403);
        }

        // Validar los datos de entrada (campos opcionales con "sometimes")
        $validated = $request->validate([
            'fecha' => 'sometimes|date',
            'hora' => 'sometimes|date_format:H:i:s',
            'semana' => 'sometimes|integer|min:1|max:52',
            'idevaluadorev' => 'sometimes|exists:usuario,id',
            'idpolinizadorev' => 'sometimes|exists:operario,id',
            'idloteev' => 'sometimes|exists:lote,id',
            'fotopath' => 'nullable|string|max:255',
            'firmapath' => 'nullable|string|max:255',
            'timestamp' => 'nullable|date',
        ]);
        
        // Si está cambiando el lote y es evaluador, verificar que el nuevo lote pertenezca a su finca
        if ($usuario->soloAccesoAFinca() && isset($validated['idloteev'])) {
            $loteId = $validated['idloteev'];
            $lote = \App\Models\Lote::find($loteId);
            
            if (!$lote || $lote->idFinca != $usuario->idFinca) {
                return response()->json(['error' => 'No tiene permisos para asignar la evaluación a este lote'], 403);
            }
        }

        // Actualizar la evaluación
        $evaluacion->update($validated);
        
        // Cargar relaciones para la respuesta
        $evaluacion->load(['evaluador', 'polinizador', 'lote']);
        
        return response()->json($evaluacion, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $usuario = $request->user();
        
        // Verificar que el usuario tiene permisos para eliminar
        if (!$usuario->puedeEliminar()) {
            return response()->json(['error' => 'No tiene permisos para eliminar evaluaciones'], 403);
        }
        
        $evaluacion = EvaluacionGeneral::with('lote')->find($id);
        if (!$evaluacion) {
            return response()->json(['error' => 'Evaluación general no encontrada'], 404);
        }
        
        // Si el usuario es evaluador, verificar que la evaluación pertenezca a su finca
        if ($usuario->soloAccesoAFinca() && $evaluacion->lote->idFinca != $usuario->idFinca) {
            return response()->json(['error' => 'No tiene permisos para eliminar esta evaluación'], 403);
        }

        $evaluacion->delete();
        return response()->json(null, 204);
    }
    
    /**
     * Get evaluaciones generales by finca id
     */
    public function getByFincaId(Request $request, string $fincaId)
    {
        try {
            $usuario = $request->user();
            
            // Si el usuario es evaluador, solo puede ver evaluaciones de su propia finca
            if ($usuario->soloAccesoAFinca() && $fincaId != $usuario->idFinca) {
                return response()->json(['error' => 'No tiene permisos para ver evaluaciones de esta finca'], 403);
            }
            
            // Obtenemos todas las evaluaciones con sus relaciones
            $evaluaciones = EvaluacionGeneral::with([
                'evaluador', 
                'polinizador', 
                'lote.finca', 
                'evaluacionesPolinizacion'
            ])
            ->whereHas('lote', function($query) use ($fincaId) {
                $query->where('idFinca', $fincaId);
            })
            ->orderBy('fecha', 'desc')
            ->get();

            if ($evaluaciones->isEmpty()) {
                return response()->json(
                    [
                        'mensaje' => 'No se encontraron evaluaciones para la finca ID: ' . $fincaId,
                        'evaluaciones' => [],
                        'evaluacionesPorFecha' => [],
                        'evaluacionesPorOperario' => []
                    ], 
                    200
                );
            }
            
            // Formatear evaluaciones para el frontend
            $evaluacionesFormateadas = $evaluaciones->map(function ($ev) {
                return [
                    'id' => $ev->id,
                    'fecha' => $ev->fecha,
                    'hora' => substr($ev->hora, 0, 5),
                    'semana' => $ev->semana,
                    'evaluador' => $ev->evaluador ? $ev->evaluador->nombre : 'N/A',
                    'polinizador' => $ev->polinizador ? $ev->polinizador->nombre : 'N/A',
                    'lote' => $ev->lote ? $ev->lote->descripcion : 'N/A',
                    'finca' => $ev->lote && $ev->lote->finca ? $ev->lote->finca->descripcion : 'N/A',
                    'fotopath' => $ev->fotopath,
                    'firmapath' => $ev->firmapath,
                    'evaluacionesPolinizacion' => $ev->evaluacionesPolinizacion
                ];
            });
            
            // Agrupar por fecha
            $evaluacionesPorFecha = [];
            foreach ($evaluacionesFormateadas as $ev) {
                if (!isset($evaluacionesPorFecha[$ev['fecha']])) {
                    $evaluacionesPorFecha[$ev['fecha']] = [];
                }
                $evaluacionesPorFecha[$ev['fecha']][] = $ev;
            }
            
            // Agrupar por operario
            $evaluacionesPorOperario = [];
            foreach ($evaluacionesFormateadas as $ev) {
                if (!isset($evaluacionesPorOperario[$ev['polinizador']])) {
                    $evaluacionesPorOperario[$ev['polinizador']] = [];
                }
                $evaluacionesPorOperario[$ev['polinizador']][] = $ev;
            }
                
            return response()->json([
                'evaluaciones' => $evaluacionesFormateadas,
                'evaluacionesPorFecha' => $evaluacionesPorFecha,
                'evaluacionesPorOperario' => $evaluacionesPorOperario
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las evaluaciones generales de la finca: ' . $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Get evaluaciones generales by fecha
     */
    public function getByFecha(Request $request)
    {
        try {
            $usuario = $request->user();
            
            $request->validate([
                'fecha' => 'required|date_format:d/m/Y'
            ]);

            $query = EvaluacionGeneral::with(['evaluador', 'polinizador', 'lote'])
                ->where('fecha', $request->fecha);
            
            // Filtrar según el tipo de rol
            if (!$usuario->tieneAccesoTotal()) {
                // Si es evaluador, solo ve las evaluaciones de su finca
                if ($usuario->soloAccesoAFinca()) {
                    $query->whereHas('lote', function($q) use ($usuario) {
                        $q->where('idFinca', $usuario->idFinca);
                    });
                } else {
                    // Si es operario, solo ve sus propias evaluaciones
                    $query->where(function($q) use ($usuario) {
                        $q->where('idevaluadorev', $usuario->id)
                          ->orWhere('idpolinizadorev', $usuario->id);
                    });
                }
            }
            
            $evaluaciones = $query->get();
                
            return response()->json($evaluaciones, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las evaluaciones generales por fecha: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get evaluaciones generales by operario
     */
    public function getByOperario(Request $request, string $operarioId)
    {
        try {
            $usuario = $request->user();
            
            // Si es un operario, solo puede ver sus propias evaluaciones
            if (!$usuario->tieneAccesoTotal() && !$usuario->soloAccesoAFinca() && $operarioId != $usuario->id) {
                return response()->json(['error' => 'No tiene permisos para ver evaluaciones de este operario'], 403);
            }
            
            $query = EvaluacionGeneral::with(['evaluador', 'polinizador', 'lote'])
                ->where('idpolinizadorev', $operarioId);
            
            // Si es evaluador, filtrar por su finca
            if ($usuario->soloAccesoAFinca()) {
                $query->whereHas('lote', function($q) use ($usuario) {
                    $q->where('idFinca', $usuario->idFinca);
                });
            }
            
            $evaluaciones = $query->get();
                
            return response()->json($evaluaciones, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las evaluaciones generales por operario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Muestra las evaluaciones relacionadas con el usuario autenticado.
     */
    public function misEvaluaciones(Request $request)
    {
        $usuario = $request->user();
        
        // Obtener evaluaciones donde el usuario es evaluador o polinizador
        $evaluaciones = EvaluacionGeneral::with(['evaluador', 'polinizador', 'lote'])
            ->where(function($query) use ($usuario) {
                $query->where('idevaluadorev', $usuario->id)
                      ->orWhere('idpolinizadorev', $usuario->id);
            })
            ->orderBy('fecha', 'desc')
            ->get();
        
        // Formatear evaluaciones para el frontend
        $evaluacionesFormateadas = $evaluaciones->map(function ($ev) {
            return [
                'id' => $ev->id,
                'fecha' => date('d/m/Y', strtotime($ev->fecha)),
                'hora' => substr($ev->hora, 0, 5),
                'semana' => $ev->semana,
                'evaluador' => $ev->evaluador ? $ev->evaluador->nombre : 'N/A',
                'polinizador' => $ev->polinizador ? $ev->polinizador->nombre : 'N/A',
                'lote' => $ev->lote ? $ev->lote->descripcion : 'N/A',
                'finca' => $ev->lote && $ev->lote->finca ? $ev->lote->finca->descripcion : 'N/A',
                'fotopath' => $ev->fotopath,
                'firmapath' => $ev->firmapath,
            ];
        });
        
        return response()->json([
            'evaluaciones' => $evaluacionesFormateadas
        ], 200);
    }
}