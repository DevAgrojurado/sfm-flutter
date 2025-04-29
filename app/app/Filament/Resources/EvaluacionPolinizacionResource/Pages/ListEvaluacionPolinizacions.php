<?php

namespace App\Filament\Resources\EvaluacionPolinizacionResource\Pages;

use App\Filament\Resources\EvaluacionPolinizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluacionPolinizacions extends ListRecords
{
    protected static string $resource = EvaluacionPolinizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
