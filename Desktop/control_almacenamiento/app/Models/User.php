<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
        'storage_used',
        'storage_limit',
    ];

    /**
     * Atributos ocultos para serialización.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos casteados.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Permitir acceso al panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Solo usuarios autenticados con rol válido
        return $this->hasAnyRole(['Administrador', 'Usuario']);
    }


    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }


    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }


    public function isAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }


    public function isUsuario(): bool
    {
        return $this->hasRole('Usuario');
    }


    public function getQuotaLimit(): int
    {
        // Define aquí la cuota máxima de almacenamiento por usuario.
        // Ejemplo: 100 MB
        return 1024 * 1024 * 100; // 100 MB en bytes
    }

    public function getUsedStorage(): int
    {
        // Calcula el total de almacenamiento usado por el usuario.
        // Asegúrate de tener el modelo File relacionado con el usuario.
        return $this->hasMany(\App\Models\File::class, 'user_id')
                    ->sum('size');
    }



    /**
     * Verifica si el usuario puede subir un archivo de cierto tamaño.
     */
    public function canUpload(int $fileSize): bool
    {
        return ($this->storage_used + $fileSize) <= $this->getQuotaLimit();
    }
}
