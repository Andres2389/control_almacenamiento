<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\File;
use App\Models\Group;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsuarios = User::count();
        $totalArchivos = File::count();
        $totalAlmacenamiento = File::sum('size') / 1048576; // MB

        $totalGrupos = Group::count();

        return [
            Stat::make('Usuarios registrados', $totalUsuarios)
                ->description('Total de cuentas en el sistema')
                ->color('primary'),

            Stat::make('Archivos subidos', $totalArchivos)
                ->description('Número total de archivos almacenados')
                ->color('success'),

            Stat::make('Almacenamiento usado', number_format($totalAlmacenamiento, 2) . ' MB')
                ->description('Espacio total utilizado en el sistema')
                ->color('warning'),

            Stat::make('Grupos creados', $totalGrupos)
                ->description('Cantidad de grupos en la plataforma')
                ->color('info'),
        ];
    }
}
