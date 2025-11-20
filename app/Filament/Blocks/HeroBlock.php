<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Builder\Block;

class HeroBlock
{
    public static function make(): Block
    {
        return Block::make('hero')
            ->label('🎯 Hero Bölümü')
            ->schema([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ana başlığınızı girin'),
                
                Textarea::make('subtitle')
                    ->label('Alt Başlık')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Açıklama metni'),
                
                TextInput::make('button_text')
                    ->label('Buton Metni')
                    ->maxLength(50)
                    ->placeholder('Örn: Daha Fazla Bilgi'),
                
                TextInput::make('button_url')
                    ->label('Buton URL')
                    ->url()
                    ->placeholder('https://...'),
                
                FileUpload::make('background_image')
                    ->label('Arka Plan Görseli')
                    ->image()
                    ->directory('hero-backgrounds')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '21:9',
                    ])
                    ->helperText('Önerilen boyut: 1920x1080px'),
            ])
            ->columns(2);
    }
}
