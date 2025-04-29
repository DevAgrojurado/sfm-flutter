<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operario extends Model
{
    protected $table = 'operario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['codigo', 'nombre', 'cargoId', 'areaId', 'fincaId', 'activo'];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargoId');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'areaId');
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class, 'fincaId');
    }

    public function evaluacionesGenerales()
    {
        return $this->hasMany(EvaluacionGeneral::class, 'idpolinizadorev');
    }

    public function evaluacionesPolinizacion()
    {
        return $this->hasMany(EvaluacionPolinizacion::class, 'idpolinizador');
    }
}