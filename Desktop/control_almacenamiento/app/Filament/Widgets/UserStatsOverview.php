<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\File;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $archivos = File::where('user_id', $user->id)->count();
        $usado = $user->getUsedStorage() / 1048576; // MB
        $limite = $user->getQuotaLimit() / 1048576; // MB
        $porcentaje = $limite > 0 ? round(($usado / $limite) * 100, 1) : 0;

        return [
            Stat::make('Archivos subidos', $archivos)
                ->description('Tus archivos en el sistema')
                ->color('success'),

            Stat::make('Espacio usado', number_format($usado, 2) . ' MB')
                ->description("De un total de {$limite} MB")
                ->color('warning'),

            Stat::make('Porcentaje ocupado', "{$porcentaje}%")
                ->description('Tu cuota de almacenamiento')
                ->color($porcentaje > 90 ? 'danger' : 'primary'),
        ];
    }
}
