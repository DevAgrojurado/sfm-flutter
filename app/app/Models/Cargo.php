<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['descripcion'];

    public function operarios()
    {
        return $this->hasMany(Operario::class, 'cargoId');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idCargo');
    }
}