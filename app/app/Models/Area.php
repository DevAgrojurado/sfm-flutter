<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['descripcion'];

    public function operarios()
    {
        return $this->hasMany(Operario::class, 'areaId');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idArea');
    }
}