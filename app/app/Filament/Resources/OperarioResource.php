<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperarioResource\Pages;
use App\Filament\Resources\OperarioResource\RelationManagers;
use App\Models\Operario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperarioResource extends Resource
{
    protected static ?string $model = Operario::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('cargoId')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('areaId')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('fincaId')
                    ->numeric()
                    ->default(null),
                Forms\Components\Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cargoId')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('areaId')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fincaId')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOperarios::route('/'),
            'create' => Pages\CreateOperario::route('/create'),
            'edit' => Pages\EditOperario::route('/{record}/edit'),
        ];
    }
}
