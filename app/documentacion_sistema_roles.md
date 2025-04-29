# Documentación del Sistema de Roles

## Descripción General

Se ha implementado un sistema de roles basado en el campo `tipo_rol` para usuarios, que permite controlar el acceso a diferentes partes de la aplicación sin modificar el campo `rol` existente que se usa para otros propósitos.

## Roles Disponibles

El sistema incluye 4 tipos de roles con diferentes niveles de acceso:

1. **Administrador**: Acceso completo a toda la aplicación. Puede crear, editar y eliminar registros.
2. **Coordinador**: Acceso a toda la aplicación, pero solo puede agregar registros. No puede editar ni eliminar.
3. **Evaluador**: Acceso limitado a la finca que pertenece. Puede agregar registros, pero no editar ni eliminar.
4. **Operario**: Acceso muy limitado, solo a lo relacionado con él mismo. No puede agregar, editar ni eliminar.

## Implementación Técnica

### Cambios en la Base de Datos

Se agregó un nuevo campo `tipo_rol` a la tabla `usuario` con los siguientes valores posibles:
- 'administrador'
- 'coordinador'
- 'evaluador'
- 'operario'

### Trait de Roles (TieneRolTrait)

Se creó un trait que proporciona métodos útiles para verificar permisos según el tipo de rol:

- `esAdministrador()`: Verifica si el usuario es administrador
- `esCoordinador()`: Verifica si el usuario es coordinador
- `esEvaluador()`: Verifica si el usuario es evaluador
- `esOperario()`: Verifica si el usuario es operario
- `puedeAgregar()`: Verifica si el usuario puede agregar registros
- `puedeEditar()`: Verifica si el usuario puede editar registros
- `puedeEliminar()`: Verifica si el usuario puede eliminar registros
- `tieneAccesoTotal()`: Verifica si el usuario tiene acceso completo a la aplicación
- `soloAccesoAFinca()`: Verifica si el usuario solo debe tener acceso a su finca

### Middleware de Rol (TipoRolMiddleware)

Se implementó un middleware que verifica los permisos según el tipo de rol del usuario. Este middleware permite proteger rutas de API según los permisos necesarios.

## Cómo Usar el Sistema de Roles

### En Controllers

Puedes verificar permisos dentro de tus controllers:

```php
public function store(Request $request)
{
    if (!$request->user()->puedeAgregar()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    
    // Lógica para crear un nuevo registro
}

public function update(Request $request, $id)
{
    if (!$request->user()->puedeEditar()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    
    // Lógica para actualizar un registro
}
```

### En Rutas (Routes)

Puedes proteger rutas usando el middleware TipoRolMiddleware:

```php
// Rutas solo para administradores
Route::middleware(['auth:sanctum', 'tipo_rol:administrador'])->group(function () {
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);
});

// Rutas para usuarios que pueden agregar registros
Route::middleware(['auth:sanctum', 'tipo_rol:agregar'])->group(function () {
    Route::post('/evaluaciones', [EvaluacionController::class, 'store']);
});
```

### En Vistas/Frontend

Puedes usar el sistema de roles para mostrar/ocultar elementos en la UI:

```php
@if(auth()->user()->puedeEditar())
    <button class="edit-button">Editar</button>
@endif

@if(auth()->user()->puedeEliminar())
    <button class="delete-button">Eliminar</button>
@endif
```

## Consejos para la Implementación

1. **Filtrado de Datos**: Para usuarios con acceso limitado (como evaluadores), asegúrate de filtrar datos en las consultas:

```php
// Para evaluadores que solo deben ver su finca
if ($user->soloAccesoAFinca()) {
    $query->where('idFinca', $user->idFinca);
}
```

2. **Cambio de Rol**: Para cambiar el rol de un usuario, simplemente actualiza el campo tipo_rol:

```php
$usuario->tipo_rol = 'administrador';
$usuario->save();
```

3. **Asignación de Rol Predeterminado**: Al crear nuevos usuarios, asegúrate de asignarles un rol predeterminado (generalmente 'operario'):

```php
$usuario = new Usuario();
// ... otros campos
$usuario->tipo_rol = 'operario';
$usuario->save();
```

## Notas Importantes

- El sistema de roles es independiente del campo `rol` existente, que se mantiene sin modificaciones.
- Siempre verifica los permisos en el backend, incluso si ya los verificaste en el frontend.
- Considera implementar pruebas automatizadas para asegurar que los permisos funcionen correctamente. 