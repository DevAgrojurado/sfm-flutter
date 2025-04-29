<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluacionPolinizacionResource\Pages;
use App\Filament\Resources\EvaluacionPolinizacionResource\RelationManagers;
use App\Models\EvaluacionPolinizacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EvaluacionPolinizacionResource extends Resource
{
    protected static ?string $model = EvaluacionPolinizacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('evaluaciongeneralid')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('fecha')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('hora'),
                Forms\Components\TextInput::make('semana')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('ubicacion')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('idevaluador')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('idpolinizador')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('idlote')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('seccion')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('palma')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('inflorescencia')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('antesis')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('antesisDejadas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('postantesisDejadas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('postantesis')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('espate')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('aplicacion')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('marcacion')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('repaso1')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('repaso2')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('observaciones')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('timestamp')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evaluaciongeneralid')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hora'),
                Tables\Columns\TextColumn::make('semana')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('idevaluador')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('idpolinizador')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('idlote')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seccion')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('palma')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inflorescencia')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('antesis')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('antesisDejadas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('postantesisDejadas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('postantesis')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('espate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aplicacion')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marcacion')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('repaso1')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('repaso2')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListEvaluacionPolinizacions::route('/'),
            'create' => Pages\CreateEvaluacionPolinizacion::route('/create'),
            'edit' => Pages\EditEvaluacionPolinizacion::route('/{record}/edit'),
        ];
    }
}
