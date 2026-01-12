<?php

namespace App\Filament\Resources\EventCategoryResource\Schemas;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Kategori')
                    ->description('Informasi dasar tentang kategori event')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama kategori')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state ?? ''));
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Auto-generate dari nama kategori')
                            ->helperText('Slug akan otomatis dibuat dari nama kategori, tapi bisa diubah manual')
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsikan kategori ini')
                            ->columnSpanFull(),
                        FileUpload::make('icon')
                            ->label('Logo Kategori')
                            ->image()
                            ->disk('public')
                            ->directory('event-category-icons')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->imagePreviewHeight('128')
                            ->downloadable()
                            ->openable()
                            ->preserveFilenames()
                            ->maxSize(2048)
                            ->required()
                            ->helperText('Upload logo untuk kategori ini (disarankan: 128x128px)')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
