<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\UserStatsOverview;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de Control';

    public function getColumns(): int | array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        $user = auth()->user();

       if ($user->hasRole('Administrador')) {
            return [AdminStatsOverview::class];
        }


       if ($user->hasRole('Usuario')) {
        
         return [UserStatsOverview::class];

        }

        return [];
    }
}
