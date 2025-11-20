<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        try {
            $analytics = app(AnalyticsService::class);
            $stats = $analytics->getVisitorStats(30);
            
            return [
                Stat::make('Aktif Kullanıcılar', number_format($stats['activeUsers']))
                    ->description('Son 30 gün')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('success')
                    ->chart([7, 12, 15, 18, 14, 22, 25]),
                
                Stat::make('Oturumlar', number_format($stats['sessions']))
                    ->description('Son 30 gün')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('warning')
                    ->chart([10, 15, 18, 20, 16, 24, 28]),
                
                Stat::make('Sayfa Görüntülenme', number_format($stats['pageViews']))
                    ->description('Son 30 gün')
                    ->descriptionIcon('heroicon-m-eye')
                    ->color('primary')
                    ->chart([15, 20, 25, 30, 28, 35, 40]),
                
                Stat::make('Ort. Oturum Süresi', gmdate('i:s', (int) $stats['avgSessionDuration']))
                    ->description('Dakika:Saniye')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('info'),
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Hata', 'Analytics API hatası')
                    ->description($e->getMessage())
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }
}
