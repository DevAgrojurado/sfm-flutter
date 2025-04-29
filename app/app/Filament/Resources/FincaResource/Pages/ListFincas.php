<?php

namespace App\Filament\Resources\FincaResource\Pages;

use App\Filament\Resources\FincaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFincas extends ListRecords
{
    protected static string $resource = FincaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
