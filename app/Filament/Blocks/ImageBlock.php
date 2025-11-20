<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder\Block;

class ImageBlock
{
    public static function make(): Block
    {
        return Block::make('image')
            ->label('🖼️ Görsel Bloğu')
            ->schema([
                FileUpload::make('image')
                    ->label('Görsel')
                    ->image()
                    ->required()
                    ->directory('images')
                    ->imageEditor()
                    ->maxSize(5120)
                    ->helperText('Maksimum boyut: 5MB'),
                
                TextInput::make('caption')
                    ->label('Açıklama')
                    ->maxLength(255)
                    ->placeholder('Görsel açıklaması (alt text)'),
                
                Select::make('alignment')
                    ->label('Hizalama')
                    ->options([
                        'left' => 'Sol',
                        'center' => 'Orta',
                        'right' => 'Sağ',
                        'full' => 'Tam Genişlik',
                    ])
                    ->default('center')
                    ->required(),
                
                Select::make('size')
                    ->label('Boyut')
                    ->options([
                        'small' => 'Küçük',
                        'medium' => 'Orta',
                        'large' => 'Büyük',
                    ])
                    ->default('medium')
                    ->required(),
            ])
            ->columns(2);
    }
}
