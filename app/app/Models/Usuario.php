<?php

namespace App\Models;

use App\Traits\TieneRolTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable, TieneRolTrait;

    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codigo', 'nombre', 'cedula', 'email', 'clave', 'idCargo', 'idArea', 'idFinca', 'rol', 'tipo_rol', 'vigente'
    ];

    protected $hidden = ['clave'];

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'idCargo');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea');
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class, 'idFinca');
    }

    public function evaluacionesGenerales()
    {
        return $this->hasMany(EvaluacionGeneral::class, 'idevaluadorev');
    }

    public function evaluacionesPolinizacion()
    {
        return $this->hasMany(EvaluacionPolinizacion::class, 'idevaluador');
    }
}