<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder\Block;

class TestimonialsBlock
{
    public static function make(): Block
    {
        return Block::make('testimonials')
            ->label('💬 Referanslar Bloğu')
            ->schema([
                TextInput::make('section_title')
                    ->label('Bölüm Başlığı')
                    ->default('Müşterilerimiz Ne Diyor?')
                    ->maxLength(255),
                
                Repeater::make('testimonials')
                    ->label('Referanslar')
                    ->schema([
                        Textarea::make('quote')
                            ->label('Alıntı')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Müşteri yorumu...'),
                        
                        TextInput::make('author')
                            ->label('İsim')
                            ->required()
                            ->maxLength(100),
                        
                        TextInput::make('position')
                            ->label('Pozisyon / Şirket')
                            ->maxLength(100)
                            ->placeholder('Örn: CEO, ABC Şirketi'),
                        
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('testimonials')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->helperText('Profil fotoğrafı (isteğe bağlı)'),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['author'] ?? null)
                    ->collapsible()
                    ->minItems(1)
                    ->maxItems(12)
                    ->defaultItems(3)
                    ->columns(1),
            ]);
    }
}
