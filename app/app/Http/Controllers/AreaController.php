<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        
        // Si el usuario es evaluador, solo puede ver áreas relacionadas con su finca
        if ($usuario->soloAccesoAFinca()) {
            // Para áreas, mostramos todas porque son datos básicos
            // pero podrías implementar una restricción si fuera necesario
        }
        
        return Area::all();
    }

    public function show(Request $request, $id)
    {
        $usuario = $request->user();
        $area = Area::findOrFail($id);
        
        // Si el usuario es evaluador, verifica si tiene acceso a esta área
        if ($usuario->soloAccesoAFinca()) {
            // Para áreas, permitimos el acceso a los datos básicos
            // pero podrías implementar una restricción si fuera necesario
        }
        
        return $area;
    }
}