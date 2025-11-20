<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Builder\Block;

class TextBlock
{
    public static function make(): Block
    {
        return Block::make('text')
            ->label('📝 Metin Bloğu')
            ->schema([
                TextInput::make('heading')
                    ->label('Başlık')
                    ->maxLength(255)
                    ->placeholder('Bölüm başlığı (isteğe bağlı)'),
                
                RichEditor::make('content')
                    ->label('İçerik')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'heading',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                        'codeBlock',
                    ])
                    ->placeholder('Metin içeriğinizi buraya yazın...'),
            ]);
    }
}
