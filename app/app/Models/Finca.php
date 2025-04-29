<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finca extends Model
{
    protected $table = 'finca';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['descripcion'];

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'idFinca');
    }

    public function operarios()
    {
        return $this->hasMany(Operario::class, 'fincaId');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idFinca');
    }
}