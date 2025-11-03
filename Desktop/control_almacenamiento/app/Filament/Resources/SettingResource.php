<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-cog';


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
            Forms\Components\TextInput::make('key')
                ->label('Clave')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('value')
                ->label('Valor')
                ->rows(3)
                ->placeholder('Ejemplo: 10MB, exe, bat, js...'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Configuración'),
                Tables\Columns\TextColumn::make('value')->label('Valor')->limit(50),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
