<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\Widget;

class RealtimeWidget extends Widget
{
    protected static string $view = 'filament.widgets.realtime-widget';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';
    
    public function getRealtimeUsers(): int
    {
        try {
            $analytics = app(AnalyticsService::class);
            return $analytics->getRealtimeUsers();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public function getTrafficSources(): array
    {
        try {
            $analytics = app(AnalyticsService::class);
            return $analytics->getTrafficSources(5);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public function getDeviceStats(): array
    {
        try {
            $analytics = app(AnalyticsService::class);
            $devices = $analytics->getDeviceStats();
            
            $total = array_sum(array_column($devices, 'users'));
            
            $result = [];
            foreach ($devices as $device => $data) {
                $percentage = $total > 0 ? round(($data['users'] / $total) * 100, 1) : 0;
                $result[] = [
                    'name' => $device,
                    'users' => $data['users'],
                    'sessions' => $data['sessions'],
                    'percentage' => $percentage,
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
}
