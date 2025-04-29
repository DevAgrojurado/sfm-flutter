<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionPolinizacion extends Model
{
    protected $table = 'evaluacionpolinizacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'evaluaciongeneralid', 'fecha', 'hora', 'semana', 'ubicacion', 'idevaluador',
        'idpolinizador', 'idlote', 'seccion', 'palma', 'inflorescencia', 'antesis',
        'antesisDejadas', 'postantesisDejadas', 'postantesis', 'espate', 'aplicacion',
        'marcacion', 'repaso1', 'repaso2', 'observaciones', 'timestamp'
    ];

    public function evaluacionGeneral()
    {
        return $this->belongsTo(EvaluacionGeneral::class, 'evaluaciongeneralid');
    }

    public function evaluador()
    {
        return $this->belongsTo(Usuario::class, 'idevaluador');
    }

    public function polinizador()
    {
        return $this->belongsTo(Operario::class, 'idpolinizador');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'idlote');
    }
}