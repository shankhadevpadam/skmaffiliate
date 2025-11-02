<?php

namespace App\Filament\Widgets;

use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    /* protected function getHeading(): ?string
    {
        return 'Analytics';
    } */

    protected function getStats(): array
    {
        $totalSubscribers = Subscriber::query()->count();

        return [
            Stat::make('Total Subscribers', $totalSubscribers)
                ->description($totalSubscribers . ' subscribers'),
        ];
    }
}
