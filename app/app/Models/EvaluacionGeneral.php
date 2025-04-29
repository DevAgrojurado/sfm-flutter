<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionGeneral extends Model
{
    protected $table = 'evaluaciongeneral';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha', 'hora', 'semana', 'idevaluadorev', 'idpolinizadorev', 'idloteev',
        'fotopath', 'firmapath', 'timestamp'
    ];

    public function evaluador()
    {
        return $this->belongsTo(Usuario::class, 'idevaluadorev');
    }

    public function polinizador()
    {
        return $this->belongsTo(Operario::class, 'idpolinizadorev');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'idloteev');
    }

    public function evaluacionesPolinizacion()
    {
        return $this->hasMany(EvaluacionPolinizacion::class, 'evaluaciongeneralid');
    }
}