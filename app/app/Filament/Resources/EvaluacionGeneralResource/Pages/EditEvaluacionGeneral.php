<?php

namespace App\Filament\Resources\EvaluacionGeneralResource\Pages;

use App\Filament\Resources\EvaluacionGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluacionGeneral extends EditRecord
{
    protected static string $resource = EvaluacionGeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
