<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Builder as FormBuilder;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Sayfalar';
    
    protected static ?string $modelLabel = 'Sayfa';
    
    protected static ?string $pluralModelLabel = 'Sayfalar';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        // İçerik Tab
                        Forms\Components\Tabs\Tab::make('📝 İçerik')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Sayfa Başlığı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                                    ->placeholder('Örn: Ana Sayfa, Hakkımızda'),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL (Slug)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Otomatik oluşturulur, gerekirse düzenleyebilirsiniz')
                                    ->placeholder('ana-sayfa'),
                                
                                Forms\Components\FileUpload::make('featured_image')
                                    ->label('Öne Çıkan Görsel')
                                    ->image()
                                    ->directory('pages')
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->helperText('Sayfa için görsel (opsiyonel)'),
                                
                                // Page Builder
                                FormBuilder::make('content')
                                    ->label('Sayfa İçeriği')
                                    ->blocks([
                                        \App\Filament\Blocks\HeroBlock::make(),
                                        \App\Filament\Blocks\TextBlock::make(),
                                        \App\Filament\Blocks\FeaturesBlock::make(),
                                        \App\Filament\Blocks\ImageBlock::make(),
                                        \App\Filament\Blocks\TestimonialsBlock::make(),
                                        \App\Filament\Blocks\ContactBlock::make(),
                                    ])
                                    ->collapsible()
                                    ->blockNumbers(false)
                                    ->columnSpanFull(),
                            ]),
                        
                        // SEO Tab
                        Forms\Components\Tabs\Tab::make('🔍 SEO')
                            ->schema([
                                Forms\Components\TextInput::make('seo_title')
                                    ->label('SEO Başlığı')
                                    ->maxLength(60)
                                    ->helperText('Google\'da görünecek başlık (max 60 karakter)')
                                    ->placeholder('Varsayılan olarak sayfa başlığı kullanılır'),
                                
                                Forms\Components\Textarea::make('seo_description')
                                    ->label('Meta Açıklama')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Google\'da görünecek açıklama (120-160 karakter ideal)')
                                    ->placeholder('Sayfanızın kısa açıklaması...'),
                                
                                Forms\Components\TagsInput::make('seo_keywords')
                                    ->label('Anahtar Kelimeler')
                                    ->helperText('SEO için anahtar kelimeler (virgülle ayırın)')
                                    ->placeholder('kelime1, kelime2, kelime3'),
                            ]),
                        
                        // Yayınlama Tab
                        Forms\Components\Tabs\Tab::make('📅 Yayınlama')
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayında')
                                    ->default(false)
                                    ->inline(false)
                                    ->helperText('Sayfayı sitede yayınla'),
                                
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Yayın Tarihi')
                                    ->default(now())
                                    ->helperText('Sayfa ne zaman yayınlanacak'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('URL kopyalandı!')
                    ->color('gray')
                    ->icon('heroicon-o-link'),
                
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Görsel')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                
                Tables\Filters\Filter::make('published')
                    ->label('Yayında Olanlar')
                    ->query(fn (Builder $query): Builder => $query->published()),
                
                Tables\Filters\Filter::make('draft')
                    ->label('Taslaklar')
                    ->query(fn (Builder $query): Builder => $query->draft()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Görüntüle')
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->label('Düzenle')
                        ->icon('heroicon-o-pencil'),
                    
                    Tables\Actions\Action::make('publish')
                        ->label('Yayınla')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('success')
                        ->visible(fn (Page $record): bool => !$record->is_published)
                        ->requiresConfirmation()
                        ->action(fn (Page $record) => $record->publish()),
                    
                    Tables\Actions\Action::make('unpublish')
                        ->label('Yayından Kaldır')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('warning')
                        ->visible(fn (Page $record): bool => $record->is_published)
                        ->requiresConfirmation()
                        ->action(fn (Page $record) => $record->unpublish()),
                    
                    Tables\Actions\DeleteAction::make()
                        ->label('Sil'),
                    
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Kalıcı Sil'),
                    
                    Tables\Actions\RestoreAction::make()
                        ->label('Geri Yükle'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                    
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Kalıcı Sil'),
                    
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Geri Yükle'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
