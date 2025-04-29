<?php

namespace App\Http\Controllers;

use App\Models\EvaluacionPolinizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluacionPolinizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $usuario = $request->user();
            $query = EvaluacionPolinizacion::with([
                'evaluacionGeneral',
                'evaluador',
                'polinizador',
                'lote'
            ]);
            
            // Filtrar según el tipo de rol
            if (!$usuario->tieneAccesoTotal()) {
                // Si es evaluador, solo ve las evaluaciones de su finca
                if ($usuario->soloAccesoAFinca()) {
                    $query->whereHas('lote', function($q) use ($usuario) {
                        $q->where('idFinca', $usuario->idFinca);
                    });
                } else {
                    // Si es operario, solo ve evaluaciones donde participa
                    $query->where(function($q) use ($usuario) {
                        $q->where('idevaluador', $usuario->id)
                          ->orWhere('idpolinizador', $usuario->id);
                    });
                }
            }
            
            $evaluacionesPolinizacion = $query->get();

            return response()->json($evaluacionesPolinizacion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las evaluaciones de polinización: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $usuario = $request->user();
            
            // Verificar que el usuario tiene permisos para agregar
            if (!$usuario->puedeAgregar()) {
                return response()->json(['error' => 'No tiene permisos para agregar evaluaciones'], 403);
            }
            
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'evaluaciongeneralid' => 'required|integer|exists:evaluaciongeneral,id',
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i:s',
                'semana' => 'required|integer',
                'ubicacion' => 'required|string|max:255',
                'idevaluador' => 'required|integer|exists:usuario,id',
                'idpolinizador' => 'required|integer|exists:operario,id',
                'idlote' => 'required|integer|exists:lote,id',
                'seccion' => 'required|string|max:255',
                'palma' => 'required|integer',
                'inflorescencia' => 'required|integer',
                'antesis' => 'required|integer',
                'antesisDejadas' => 'required|integer',
                'postantesisDejadas' => 'required|integer',
                'postantesis' => 'required|integer',
                'espate' => 'required|string|max:255',
                'aplicacion' => 'required|string|max:255',
                'marcacion' => 'required|string|max:255',
                'repaso1' => 'nullable|date',
                'repaso2' => 'nullable|date',
                'observaciones' => 'nullable|string',
                'timestamp' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            // Si el usuario es evaluador, verificar que el lote pertenezca a su finca
            if ($usuario->soloAccesoAFinca()) {
                $loteId = $request->input('idlote');
                $lote = \App\Models\Lote::find($loteId);
                
                if (!$lote || $lote->idFinca != $usuario->idFinca) {
                    return response()->json(['error' => 'No tiene permisos para crear evaluaciones en esta finca'], 403);
                }
            }

            // Crear una nueva evaluación de polinización
            $evaluacionPolinizacion = EvaluacionPolinizacion::create($request->all());

            // Cargar las relaciones para la respuesta
            $evaluacionPolinizacion->load('evaluacionGeneral', 'evaluador', 'polinizador', 'lote');

            return response()->json($evaluacionPolinizacion, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la evaluación de polinización: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $usuario = $request->user();
            
            $evaluacionPolinizacion = EvaluacionPolinizacion::with([
                'evaluacionGeneral',
                'evaluador',
                'polinizador',
                'lote.finca'
            ])->find($id);

            if (!$evaluacionPolinizacion) {
                return response()->json(['error' => 'Evaluación de polinización no encontrada'], 404);
            }
            
            // Verificar permisos según el tipo de rol
            if (!$usuario->tieneAccesoTotal()) {
                if ($usuario->soloAccesoAFinca()) {
                    // Si es evaluador, verificar que la evaluación pertenezca a su finca
                    if ($evaluacionPolinizacion->lote->idFinca != $usuario->idFinca) {
                        return response()->json(['error' => 'No tiene permisos para ver esta evaluación'], 403);
                    }
                } else {
                    // Si es operario, verificar que participe en la evaluación
                    if ($evaluacionPolinizacion->idevaluador != $usuario->id && 
                        $evaluacionPolinizacion->idpolinizador != $usuario->id) {
                        return response()->json(['error' => 'No tiene permisos para ver esta evaluación'], 403);
                    }
                }
            }

            return response()->json($evaluacionPolinizacion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la evaluación de polinización: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Buscar la evaluación de polinización
            $evaluacionPolinizacion = EvaluacionPolinizacion::find($id);

            if (!$evaluacionPolinizacion) {
                return response()->json(['error' => 'Evaluación de polinización no encontrada'], 404);
            }

            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'evaluaciongeneralid' => 'required|integer|exists:evaluaciongeneral,id',
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i:s',
                'semana' => 'required|integer',
                'ubicacion' => 'required|string|max:255',
                'idevaluador' => 'required|integer|exists:usuario,id',
                'idpolinizador' => 'required|integer|exists:operario,id',
                'idlote' => 'required|integer|exists:lote,id',
                'seccion' => 'required|string|max:255',
                'palma' => 'required|integer',
                'inflorescencia' => 'required|integer',
                'antesis' => 'required|integer',
                'antesisDejadas' => 'required|integer',
                'postantesisDejadas' => 'required|integer',
                'postantesis' => 'required|integer',
                'espate' => 'required|string|max:255',
                'aplicacion' => 'required|string|max:255',
                'marcacion' => 'required|string|max:255',
                'repaso1' => 'nullable|date',
                'repaso2' => 'nullable|date',
                'observaciones' => 'nullable|string',
                'timestamp' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Actualizar la evaluación de polinización
            $evaluacionPolinizacion->update($request->all());

            // Cargar las relaciones para la respuesta
            $evaluacionPolinizacion->load('evaluacionGeneral', 'evaluador', 'polinizador', 'lote');

            return response()->json($evaluacionPolinizacion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la evaluación de polinización'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar la evaluación de polinización
            $evaluacionPolinizacion = EvaluacionPolinizacion::find($id);

            if (!$evaluacionPolinizacion) {
                return response()->json(['error' => 'Evaluación de polinización no encontrada'], 404);
            }

            // Eliminar la evaluación
            $evaluacionPolinizacion->delete();

            return response()->json(['message' => 'Evaluación de polinización eliminada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la evaluación de polinización'], 500);
        }
    }

    /**
     * Get evaluaciones polinizacion by finca id
     */
    public function getByFincaId(string $fincaId)
    {
        try {
            // Obtenemos todas las evaluaciones con sus relaciones
            $evaluaciones = EvaluacionPolinizacion::with([
                'evaluacionGeneral',
                'evaluador',
                'polinizador',
                'lote.finca'
            ])
            ->whereHas('lote', function($query) use ($fincaId) {
                $query->where('idFinca', $fincaId);
            })
            ->get();
                
            return response()->json($evaluaciones, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las evaluaciones de polinización de la finca: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get evaluaciones polinizacion by evaluacion general id
     */
    public function getByEvaluacionGeneralId(string $evaluacionGeneralId)
    {
        try {
            // Obtenemos todas las evaluaciones con sus relaciones
            $evaluaciones = EvaluacionPolinizacion::with([
                'evaluacionGeneral',
                'evaluador',
                'polinizador',
                'lote'
            ])
            ->where('evaluaciongeneralid', $evaluacionGeneralId)
            ->get();
                
            return response()->json($evaluaciones, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las evaluaciones de polinización de la evaluación general: ' . $e->getMessage()], 500);
        }
    }
}