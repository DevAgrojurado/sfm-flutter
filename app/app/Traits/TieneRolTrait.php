<?php

namespace App\Traits;

trait TieneRolTrait
{
    /**
     * Verifica si el usuario tiene el rol de administrador.
     *
     * @return bool
     */
    public function esAdministrador(): bool
    {
        return $this->tipo_rol === 'administrador';
    }

    /**
     * Verifica si el usuario tiene el rol de coordinador.
     *
     * @return bool
     */
    public function esCoordinador(): bool
    {
        return $this->tipo_rol === 'coordinador';
    }

    /**
     * Verifica si el usuario tiene el rol de evaluador.
     *
     * @return bool
     */
    public function esEvaluador(): bool
    {
        return $this->tipo_rol === 'evaluador';
    }

    /**
     * Verifica si el usuario tiene el rol de operario.
     *
     * @return bool
     */
    public function esOperario(): bool
    {
        return $this->tipo_rol === 'operario';
    }

    /**
     * Verifica si el usuario puede agregar registros.
     *
     * @return bool
     */
    public function puedeAgregar(): bool
    {
        return in_array($this->tipo_rol, ['administrador', 'coordinador', 'evaluador']);
    }

    /**
     * Verifica si el usuario puede editar registros.
     *
     * @return bool
     */
    public function puedeEditar(): bool
    {
        return in_array($this->tipo_rol, ['administrador']);
    }

    /**
     * Verifica si el usuario puede eliminar registros.
     *
     * @return bool
     */
    public function puedeEliminar(): bool
    {
        return in_array($this->tipo_rol, ['administrador']);
    }

    /**
     * Verifica si el usuario tiene acceso total a la aplicación.
     *
     * @return bool
     */
    public function tieneAccesoTotal(): bool
    {
        return in_array($this->tipo_rol, ['administrador', 'coordinador']);
    }

    /**
     * Verifica si el usuario solo tiene acceso a su finca.
     *
     * @return bool
     */
    public function soloAccesoAFinca(): bool
    {
        return $this->tipo_rol === 'evaluador';
    }
} 