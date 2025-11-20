<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder\Block;

class FeaturesBlock
{
    public static function make(): Block
    {
        return Block::make('features')
            ->label('⭐ Özellikler Bloğu')
            ->schema([
                TextInput::make('section_title')
                    ->label('Bölüm Başlığı')
                    ->maxLength(255)
                    ->placeholder('Örn: Neden Bizi Seçmelisiniz?'),
                
                Textarea::make('section_description')
                    ->label('Bölüm Açıklaması')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Kısa açıklama metni'),
                
                Repeater::make('features')
                    ->label('Özellikler')
                    ->schema([
                        Select::make('icon')
                            ->label('İkon')
                            ->options([
                                'rocket' => '🚀 Roket',
                                'shield' => '🛡️ Kalkan',
                                'star' => '⭐ Yıldız',
                                'lightning' => '⚡ Şimşek',
                                'heart' => '❤️ Kalp',
                                'sparkles' => '✨ Parıltı',
                                'trophy' => '🏆 Kupa',
                                'target' => '🎯 Hedef',
                                'chart' => '📊 Grafik',
                                'gear' => '⚙️ Ayar',
                            ])
                            ->required()
                            ->searchable(),
                        
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(100),
                        
                        Textarea::make('description')
                            ->label('Açıklama')
                            ->required()
                            ->rows(2)
                            ->maxLength(255),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                    ->collapsible()
                    ->minItems(1)
                    ->maxItems(12)
                    ->defaultItems(3)
                    ->columns(1),
            ]);
    }
}
