<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['descripcion', 'idFinca'];

    public function finca()
    {
        return $this->belongsTo(Finca::class, 'idFinca');
    }

    public function evaluacionesGenerales()
    {
        return $this->hasMany(EvaluacionGeneral::class, 'idloteev');
    }

    public function evaluacionesPolinizacion()
    {
        return $this->hasMany(EvaluacionPolinizacion::class, 'idlote');
    }
}