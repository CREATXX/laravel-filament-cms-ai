<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopPagesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        try {
            $analytics = app(AnalyticsService::class);
            $topPages = $analytics->getTopPages(10);
            
            return $table
                ->heading('En Çok Ziyaret Edilen Sayfalar (Son 30 Gün)')
                ->query(
                    // Dummy query - gerçek veri $topPages array'inden gelecek
                    \App\Models\Page::query()->whereRaw('1 = 0')
                )
                ->columns([
                    Tables\Columns\TextColumn::make('title')
                        ->label('Sayfa Başlığı')
                        ->searchable()
                        ->sortable()
                        ->weight('medium'),
                    
                    Tables\Columns\TextColumn::make('path')
                        ->label('URL')
                        ->searchable()
                        ->copyable()
                        ->color('gray'),
                    
                    Tables\Columns\TextColumn::make('pageViews')
                        ->label('Görüntülenme')
                        ->sortable()
                        ->badge()
                        ->color('success'),
                    
                    Tables\Columns\TextColumn::make('avgDuration')
                        ->label('Ort. Süre (sn)')
                        ->sortable()
                        ->formatStateUsing(fn ($state) => gmdate('i:s', (int) $state))
                        ->color('info'),
                ])
                ->paginated(false)
                // Custom data injection
                ->modifyQueryUsing(function ($query) use ($topPages) {
                    // Override edilecek
                    return $query;
                });
        } catch (\Exception $e) {
            return $table
                ->heading('En Çok Ziyaret Edilen Sayfalar - Hata')
                ->query(\App\Models\Page::query()->whereRaw('1 = 0'))
                ->columns([
                    Tables\Columns\TextColumn::make('error')
                        ->label('Hata Mesajı')
                        ->default($e->getMessage())
                        ->color('danger'),
                ]);
        }
    }
    
    /**
     * Custom data provider - Analytics API'den gelen veriyi tablo formatına çevir
     */
    protected function getTableRecords(): array
    {
        try {
            $analytics = app(AnalyticsService::class);
            return $analytics->getTopPages(10);
        } catch (\Exception $e) {
            return [];
        }
    }
}
