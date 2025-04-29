<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Usuario;

class CreateUsuario extends CreateRecord
{
    protected static string $resource = UsuarioResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que se establezca un valor por defecto para tipo_rol si no está presente
        if (!isset($data['tipo_rol']) || empty($data['tipo_rol'])) {
            $data['tipo_rol'] = 'operario';
        }
        
        return $data;
    }
}
