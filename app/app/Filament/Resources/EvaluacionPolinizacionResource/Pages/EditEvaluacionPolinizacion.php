<?php

namespace App\Filament\Resources\EvaluacionPolinizacionResource\Pages;

use App\Filament\Resources\EvaluacionPolinizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluacionPolinizacion extends EditRecord
{
    protected static string $resource = EvaluacionPolinizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
