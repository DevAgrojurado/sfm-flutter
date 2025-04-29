<?php

namespace App\Filament\Resources\EvaluacionGeneralResource\Pages;

use App\Filament\Resources\EvaluacionGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluacionGenerals extends ListRecords
{
    protected static string $resource = EvaluacionGeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
