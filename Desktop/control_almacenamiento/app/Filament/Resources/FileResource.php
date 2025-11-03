<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class FileResource extends Resource
{
    protected static ?string $model = File::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Gestión de Archivos';


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
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Usuario')
                ->required(),

            Forms\Components\TextInput::make('name')
                ->label('Nombre del archivo (opcional)')
                ->placeholder('Se usará el nombre original si se deja vacío'),

            Forms\Components\FileUpload::make('path')
                ->label('Archivo')
                ->directory('uploads')
                ->disk('public')
                ->preserveFilenames()
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    // Obtener tamaño real del archivo subido
                    if ($state && Storage::disk('public')->exists($state)) {
                        $fileSize = Storage::disk('public')->size($state);
                        $set('size', $fileSize);
                    }
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Archivo')
                    ->getStateUsing(fn($record) => $record->name ?? basename($record->path))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario'),

                Tables\Columns\TextColumn::make('size')
                    ->label('Tamaño')
                    ->formatStateUsing(fn($state) => $state ? number_format($state / 1024, 2) . ' KB' : 'N/A'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subido el')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (File $record) {
                        $disk = 'public';
                        $path = $record->path;

                        if (!Storage::disk($disk)->exists($path)) {
                            Notification::make()
                                ->title('Archivo no encontrado')
                                ->body('El archivo fue eliminado o movido del servidor.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $fileName = $record->name ?? basename($path);
                        $mimeType = Storage::disk($disk)->mimeType($path);

                        return response()->streamDownload(function () use ($disk, $path) {
                            echo Storage::disk($disk)->get($path);
                        }, $fileName, [
                            'Content-Type' => $mimeType,
                        ]);
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->color('danger'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
        ];
    }
}
