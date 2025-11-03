<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';


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
                ->label('Nombre')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->unique(ignoreRecord: true)
                ->required(),
            Forms\Components\Select::make('group_id')
                ->label('Grupo')
                ->relationship('group', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('storage_limit')
                ->label('Límite personalizado (MB)')
                ->numeric()
                ->suffix('MB'),
            Forms\Components\TextInput::make('password')
                ->password()
                ->label('Contraseña')
                ->dehydrateStateUsing(fn($state) => bcrypt($state))
                ->dehydrated(fn($state) => filled($state))
                ->required(fn(string $context) => $context === 'create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Correo'),
                Tables\Columns\TextColumn::make('group.name')->label('Grupo'),
                Tables\Columns\TextColumn::make('storage_used')
                    ->label('Usado')
                    ->formatStateUsing(fn($state) => number_format($state / 1048576, 2) . ' MB'),
                Tables\Columns\TextColumn::make('storage_limit')
                    ->label('Límite')
                    ->formatStateUsing(fn($state) => $state ? $state . ' MB' : '—'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
