<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluacionGeneralResource\Pages;
use App\Filament\Resources\EvaluacionGeneralResource\RelationManagers;
use App\Models\EvaluacionGeneral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluacionGeneralResource extends Resource
{
    protected static ?string $model = EvaluacionGeneral::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Evaluaciones';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fecha')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('hora'),
                Forms\Components\TextInput::make('semana')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('idevaluadorev')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('idpolinizadorev')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('idloteev')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('fotopath')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('firmapath')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('timestamp')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hora'),
                Tables\Columns\TextColumn::make('semana')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('idevaluadorev')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('idpolinizadorev')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('idloteev')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fotopath')
                    ->searchable(),
                Tables\Columns\TextColumn::make('firmapath')
                    ->searchable(),
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
            'index' => Pages\ListEvaluacionGenerals::route('/'),
            'create' => Pages\CreateEvaluacionGeneral::route('/create'),
            'edit' => Pages\EditEvaluacionGeneral::route('/{record}/edit'),
        ];
    }
}
