<?php

namespace App\Filament\Resources\FincaResource\Pages;

use App\Filament\Resources\FincaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinca extends EditRecord
{
    protected static string $resource = FincaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
