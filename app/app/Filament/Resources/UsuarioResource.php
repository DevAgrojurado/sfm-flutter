<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Filament\Resources\UsuarioResource\RelationManagers;
use App\Models\Usuario;
use App\Models\Cargo;
use App\Models\Area;
use App\Models\Finca;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

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
                Forms\Components\TextInput::make('cedula')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('clave')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Contraseña')
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('idCargo')
                    ->label('Cargo')
                    ->options(Cargo::all()->pluck('descripcion', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('idArea')
                    ->label('Área')
                    ->options(Area::all()->pluck('descripcion', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('idFinca')
                    ->label('Finca')
                    ->options(Finca::all()->pluck('descripcion', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('rol')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo_rol')
                    ->options([
                        'administrador' => 'Administrador',
                        'coordinador' => 'Coordinador',
                        'evaluador' => 'Evaluador',
                        'operario' => 'Operario',
                    ])
                    ->required()
                    ->default('operario'),
                Forms\Components\Toggle::make('vigente'),
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
                Tables\Columns\TextColumn::make('cedula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('idCargo')
                    ->label('Cargo')
                    ->formatStateUsing(fn ($state) => Cargo::find($state)?->descripcion ?? 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('idArea')
                    ->label('Área')
                    ->formatStateUsing(fn ($state) => Area::find($state)?->descripcion ?? 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('idFinca')
                    ->label('Finca')
                    ->formatStateUsing(fn ($state) => Finca::find($state)?->descripcion ?? 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rol')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrador' => 'success',
                        'coordinador' => 'info',
                        'evaluador' => 'warning',
                        'operario' => 'danger',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('vigente')
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
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit' => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}
