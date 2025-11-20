<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;

class VisitorChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Ziyaretçi Grafiği (Son 30 Gün)';
    
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        try {
            $analytics = app(AnalyticsService::class);
            $chartData = $analytics->getVisitorChart(30);
            
            if (empty($chartData)) {
                return [
                    'datasets' => [
                        [
                            'label' => 'Veri bulunamadı',
                            'data' => [],
                        ],
                    ],
                    'labels' => [],
                ];
            }
            
            $labels = array_column($chartData, 'date');
            $visitors = array_column($chartData, 'visitors');
            $pageViews = array_column($chartData, 'pageViews');
            
            return [
                'datasets' => [
                    [
                        'label' => 'Ziyaretçiler',
                        'data' => $visitors,
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'borderColor' => 'rgb(59, 130, 246)',
                        'fill' => true,
                    ],
                    [
                        'label' => 'Sayfa Görüntülenme',
                        'data' => $pageViews,
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'borderColor' => 'rgb(16, 185, 129)',
                        'fill' => true,
                    ],
                ],
                'labels' => $labels,
            ];
        } catch (\Exception $e) {
            return [
                'datasets' => [
                    [
                        'label' => 'Hata: ' . $e->getMessage(),
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }
    }

    protected function getType(): string
    {
        return 'line';
    }
}
