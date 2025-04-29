<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TipoRolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permiso  El permiso requerido (administrador, coordinador, evaluador, operario)
     */
    public function handle(Request $request, Closure $next, string $permiso = null): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // Si no se especifica un permiso, simplemente continúa
        if (is_null($permiso)) {
            return $next($request);
        }

        // Verificar permisos según el tipo_rol
        switch ($permiso) {
            case 'agregar':
                if (!$usuario->puedeAgregar()) {
                    return response()->json(['error' => 'No tiene permisos para agregar'], 403);
                }
                break;
            case 'editar':
                if (!$usuario->puedeEditar()) {
                    return response()->json(['error' => 'No tiene permisos para editar'], 403);
                }
                break;
            case 'eliminar':
                if (!$usuario->puedeEliminar()) {
                    return response()->json(['error' => 'No tiene permisos para eliminar'], 403);
                }
                break;
            case 'acceso_total':
                if (!$usuario->tieneAccesoTotal()) {
                    return response()->json(['error' => 'No tiene acceso total'], 403);
                }
                break;
            case 'administrador':
                if (!$usuario->esAdministrador()) {
                    return response()->json(['error' => 'Requiere rol de administrador'], 403);
                }
                break;
            case 'coordinador':
                if (!$usuario->esCoordinador() && !$usuario->esAdministrador()) {
                    return response()->json(['error' => 'Requiere rol de coordinador o superior'], 403);
                }
                break;
            case 'evaluador':
                if (!$usuario->esEvaluador() && !$usuario->esCoordinador() && !$usuario->esAdministrador()) {
                    return response()->json(['error' => 'Requiere rol de evaluador o superior'], 403);
                }
                break;
            default:
                // Si el permiso no es reconocido, simplemente continúa
                break;
        }

        return $next($request);
    }
}
