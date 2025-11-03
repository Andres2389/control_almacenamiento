<?php

namespace App\Filament\Pages;

use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MisArchivos extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static string $view = 'filament.pages.mis-archivos';
    protected static ?string $navigationLabel = 'Mis Archivos';
    protected static ?string $title = 'Gestión de Archivos';

    public ?array $data = [];
    public float $used = 0;
    public float $quota = 0;
    public float $percentage = 0;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Usuario');
    }

    public function mount(): void
    {
        if (!Auth::user()->hasRole('Usuario')) {
            abort(403);
        }

        $this->updateUserStorageStats();
        $this->form->fill();
    }

    /**
     * 🔁 Actualiza los valores de almacenamiento del usuario
     */
    protected function updateUserStorageStats(): void
    {
        $user = Auth::user();

        $this->used = $user->getUsedStorage();
        $this->quota = $user->getQuotaLimit();
        $this->percentage = $this->quota > 0 ? round(($this->used / $this->quota) * 100, 2) : 0;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subir nuevo archivo')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del archivo (opcional)')
                            ->placeholder('Deja en blanco para usar el nombre original'),

                        Forms\Components\FileUpload::make('file')
                            ->label('Seleccionar archivo')
                            ->directory(fn() => 'uploads/usuarios/' . Auth::id())
                            ->disk('public')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'image/*', 'application/pdf', 'text/plain', 'application/zip'
                            ])
                            ->maxSize(10240)
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getUserFilesQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('size')
                    ->label('Tamaño')
                    ->formatStateUsing(fn($state) => number_format($state / 1024, 2) . ' KB'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de subida')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (File $record) {
                        $disk = 'public';
                        $path = $record->path;

                        if (!Storage::disk($disk)->exists($path)) {
                            Notification::make()
                                ->title('Archivo no encontrado')
                                ->body('El archivo no existe o fue eliminado del servidor.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $fileName = basename($path);
                        return response()->download(Storage::disk($disk)->path($path), $fileName);
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->visible(fn(File $record) => $record->user_id === Auth::id())
                    ->after(function () {
                        // 🔁 Actualiza la barra después de eliminar un archivo
                        $this->updateUserStorageStats();
                        Notification::make()
                            ->title('Archivo eliminado correctamente')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private function getUserFilesQuery(): Builder
    {
        return File::query()->where('user_id', Auth::id());
    }

    public function subirArchivo(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        try {
            $quota = $user->getQuotaLimit();
            $used = $user->getUsedStorage();

            $filePath = $data['file'];
            $fileSize = Storage::disk('public')->size($filePath);

            if (($used + $fileSize) > $quota) {
                Notification::make()
                    ->title('Error')
                    ->body('Has superado tu límite de almacenamiento.')
                    ->danger()
                    ->send();
                return;
            }

            $originalName = basename($filePath);

            File::create([
                'user_id' => $user->id,
                'name' => $data['name'] ?: $originalName,
                'path' => $filePath,
                'size' => $fileSize,
            ]);

            // 🔁 Actualiza las estadísticas de usuario
            $this->updateUserStorageStats();

            Notification::make()
                ->title('Archivo subido exitosamente')
                ->success()
                ->send();

            $this->form->fill();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al subir archivo')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        return [
            'used' => $this->used,
            'quota' => $this->quota,
            'percentage' => $this->percentage,
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('subir')
                ->label('Subir Archivo')
                ->action('subirArchivo')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
