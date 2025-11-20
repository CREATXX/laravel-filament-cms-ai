<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder\Block;

class ContactBlock
{
    public static function make(): Block
    {
        return Block::make('contact')
            ->label('📞 İletişim Bloğu')
            ->schema([
                TextInput::make('heading')
                    ->label('Başlık')
                    ->default('İletişime Geçin')
                    ->maxLength(255),
                
                Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('İletişim bölümü açıklaması'),
                
                Toggle::make('show_form')
                    ->label('İletişim Formunu Göster')
                    ->default(true)
                    ->inline(false),
                
                Toggle::make('show_map')
                    ->label('Harita Göster')
                    ->default(false)
                    ->inline(false)
                    ->reactive(),
                
                TextInput::make('map_embed_url')
                    ->label('Harita Embed URL')
                    ->url()
                    ->placeholder('Google Maps iframe src URL')
                    ->helperText('Google Maps → Paylaş → Harita Yerleştir → iframe src kopyalayın')
                    ->visible(fn (callable $get) => $get('show_map')),
                
                Toggle::make('show_info')
                    ->label('İletişim Bilgilerini Göster')
                    ->default(true)
                    ->inline(false)
                    ->helperText('E-posta, telefon, adres bilgileri Ayarlar sayfasından çekilir'),
            ]);
    }
}
