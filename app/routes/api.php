<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\EvaluacionGeneralController;
use App\Http\Controllers\EvaluacionPolinizacionController;
use App\Http\Controllers\FincaController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\OperarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\TipoRolMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ruta para login (pública, no requiere autenticación)
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por autenticación (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    // Ruta común para cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // ========== RUTAS PROTEGIDAS POR TIPO DE ROL ==========
    
    // Rutas solo para Administradores (pueden crear, editar y eliminar)
    Route::middleware(TipoRolMiddleware::class . ':administrador')->group(function () {
        // CRUD completo para administradores
        Route::apiResource('areas', AreaController::class);
        Route::apiResource('cargos', CargoController::class);
        Route::apiResource('fincas', FincaController::class);
        Route::apiResource('lotes', LoteController::class);
        Route::apiResource('operarios', OperarioController::class);
        Route::apiResource('usuarios', UsuarioController::class);
    });
    
    // Rutas para Coordinadores y superiores (pueden ver todo)
    Route::middleware(TipoRolMiddleware::class . ':coordinador')->group(function () {
        // Acceso a todos los recursos con lectura
        Route::apiResource('areas', AreaController::class)->only(['index', 'show']);
        Route::apiResource('cargos', CargoController::class)->only(['index', 'show']);
        Route::apiResource('fincas', FincaController::class)->only(['index', 'show']);
        Route::apiResource('lotes', LoteController::class)->only(['index', 'show']);
        Route::apiResource('operarios', OperarioController::class)->only(['index', 'show']);
        Route::apiResource('usuarios', UsuarioController::class)->only(['index', 'show']);
        
        // Acceso a todas las evaluaciones con lectura
        Route::get('/evaluaciones-generales', [EvaluacionGeneralController::class, 'index']);
        Route::get('/evaluaciones-generales/{id}', [EvaluacionGeneralController::class, 'show']);
        Route::get('/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'index']);
        Route::get('/evaluaciones-polinizacion/{id}', [EvaluacionPolinizacionController::class, 'show']);
        
        // Rutas personalizadas para coordinadores
        Route::get('/fincas/{fincaId}/lotes', [LoteController::class, 'getByFincaId']);
        Route::get('/fincas/{fincaId}/evaluaciones-generales', [EvaluacionGeneralController::class, 'getByFincaId']);
        Route::get('/fincas/{fincaId}/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'getByFincaId']);
        Route::get('/evaluaciones-generales/{evalId}/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'getByEvaluacionGeneralId']);
        Route::get('/evaluaciones-generales/fecha', [EvaluacionGeneralController::class, 'getByFecha']);
        Route::get('/evaluaciones-generales/operario/{operarioId}', [EvaluacionGeneralController::class, 'getByOperario']);
    });
    
    // Rutas para Evaluadores y superiores (pueden agregar evaluaciones)
    Route::middleware(TipoRolMiddleware::class . ':evaluador')->group(function () {
        // Para evaluadores que pueden agregar evaluaciones
        Route::post('/evaluaciones-generales', [EvaluacionGeneralController::class, 'store']);
        Route::post('/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'store']);
        
        // Obtener datos básicos (solo para visualizar)
        Route::get('/areas', [AreaController::class, 'index']);
        Route::get('/cargos', [CargoController::class, 'index']);
        
        // Permitir a los evaluadores ver su finca y lotes relacionados
        Route::get('/fincas', [FincaController::class, 'index']);
        Route::get('/fincas/{id}', [FincaController::class, 'show']);
        Route::get('/lotes', [LoteController::class, 'index']);
        Route::get('/lotes/{id}', [LoteController::class, 'show']);
        
        // Permitir a los evaluadores ver evaluaciones relacionadas con su finca
        Route::get('/evaluaciones-generales', [EvaluacionGeneralController::class, 'index']);
        Route::get('/evaluaciones-generales/{id}', [EvaluacionGeneralController::class, 'show']);
        Route::get('/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'index']);
        Route::get('/evaluaciones-polinizacion/{id}', [EvaluacionPolinizacionController::class, 'show']);
        
        // Rutas personalizadas para evaluadores (se filtrarán en el controlador)
        Route::get('/fincas/{fincaId}/lotes', [LoteController::class, 'getByFincaId']);
        Route::get('/fincas/{fincaId}/evaluaciones-generales', [EvaluacionGeneralController::class, 'getByFincaId']);
        Route::get('/fincas/{fincaId}/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'getByFincaId']);
        Route::get('/evaluaciones-generales/{evalId}/evaluaciones-polinizacion', [EvaluacionPolinizacionController::class, 'getByEvaluacionGeneralId']);
    });
    
    // Rutas para todos los usuarios autenticados (incluyendo Operarios)
    // Estas rutas deben ser filtradas en los controladores para mostrar solo los datos relevantes para el usuario
    Route::get('/perfil', [UsuarioController::class, 'perfil']);
    
    // Para operarios y evaluadores: solo ver sus propias evaluaciones
    Route::get('/mis-evaluaciones', [EvaluacionGeneralController::class, 'misEvaluaciones']);
});