<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;
    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-users';


    public static function canViewAny(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('Usuario');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('Usuario');
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre del grupo')
                ->required(),
            Forms\Components\TextInput::make('storage_limit')
                ->label('Cuota (MB)')
                ->numeric()
                ->suffix('MB')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre'),
                Tables\Columns\TextColumn::make('storage_limit')
                    ->label('Cuota')
                    ->formatStateUsing(fn($state) => $state . ' MB'),
                Tables\Columns\TextColumn::make('users.name')
                    ->counts('users')
                    ->label('Usuarios')
                    ->formatStateUsing(fn($record) => $record->users->pluck('name')->join(', ')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
