<?php

namespace App\Filament\Resources\EventResource\Schemas;

use App\Models\EventCategory;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::getEventInformationSection(),
                static::getEventDetailsSection(),
                static::getRegistrationPricingSection(),
                static::getAdditionalPrizesSection(),
                static::getWinnerInformationSection(),
            ]);
    }

    private static function getEventInformationSection(): Section
    {
        return Section::make('Informasi Event')
            ->description('Informasi dasar tentang event')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('title')
                    ->label('Judul Event')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan judul event')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state ?? ''));
                    })
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Auto-generate dari judul event')
                    ->helperText('Slug akan otomatis dibuat dari judul event, tapi bisa diubah manual')
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(4)
                    ->placeholder('Deskripsikan event Anda secara detail')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Thumbnail Event')
                    ->image()
                    ->directory('events')
                    ->disk('public')
                    ->maxSize(10240)
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->required()
                    ->helperText('Upload thumbnail untuk event ini')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getEventDetailsSection(): Section
    {
        return Section::make('Detail Event')
            ->description('Informasi kategori, venue, dan jadwal')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                Select::make('category_id')
                    ->label('Kategori')
                    ->options(EventCategory::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih kategori event'),
                Select::make('venue_id')
                    ->label('Venue')
                    ->options(function () {
                        $user = auth()->user();

                        if ($user && $user->isVenueOwner()) {
                            return Venue::where('user_id', $user->id)->pluck('name', 'id');
                        }

                        return Venue::all()->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih lokasi venue'),
                DateTimePicker::make('date')
                    ->label('Tanggal & Waktu Event')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->placeholder('Pilih tanggal dan waktu event'),
                Select::make('status')
                    ->label('Status Event')
                    ->options([
                        'open' => 'Buka Pendaftaran',
                        'closed' => 'Pendaftaran Ditutup',
                        'ended' => 'Event Selesai',
                    ])
                    ->default('open')
                    ->required()
                    ->native(false),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getRegistrationPricingSection(): Section
    {
        return Section::make('Pendaftaran & Harga')
            ->description('Batas peserta dan informasi harga')
            ->icon('heroicon-o-currency-dollar')
            ->schema([
                TextInput::make('max_participants')
                    ->label('Maksimal Peserta')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->placeholder('Masukkan jumlah maksimal peserta')
                    ->suffix('orang'),
                TextInput::make('price')
                    ->label('Biaya Pendaftaran')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->placeholder('Masukkan biaya pendaftaran')
                    ->minValue(0),
                TextInput::make('total_prize')
                    ->label('Total Hadiah')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->placeholder('Masukkan total hadiah')
                    ->minValue(0),
                Toggle::make('is_featured')
                    ->label('Event Unggulan')
                    ->helperText('Tampilkan event ini di bagian "Paling Populer"')
                    ->default(false)
                    ->onIcon('heroicon-o-star')
                    ->offIcon('heroicon-o-star')
                    ->onColor('warning')
                    ->offColor('gray')
                    ->visible(fn() => auth()->user()?->isAdmin()),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getAdditionalPrizesSection(): Section
    {
        return Section::make('Hadiah Tambahan')
            ->description('Informasi hadiah tambahan untuk event')
            ->icon('heroicon-o-gift')
            ->schema([
                Repeater::make('prizes')
                    ->label('Daftar Hadiah')
                    ->relationship('prizes')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Hadiah')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama hadiah'),
                        TextInput::make('given_by')
                            ->label('Diberikan Oleh')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama pemberi hadiah'),
                        FileUpload::make('image')
                            ->label('Gambar Hadiah')
                            ->image()
                            ->directory('event-prizes')
                            ->disk('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Hadiah')
                    ->reorderable()
                    ->collapsible(),
            ])
            ->columns(1)
            ->columnSpanFull();
    }

    private static function getWinnerInformationSection(): Section
    {
        return Section::make('Informasi Pemenang')
            ->description('Atur pemenang event (hanya untuk event yang sudah selesai)')
            ->icon('heroicon-o-trophy')
            ->schema([
                TextInput::make('winner_name')
                    ->label('Nama Pemenang')
                    ->maxLength(255)
                    ->placeholder('Masukkan nama pemenang')
                    ->helperText('Biarkan kosong jika belum ada pemenang'),
                TextInput::make('winner_number')
                    ->label('Nomor Pemenang')
                    ->maxLength(10)
                    ->placeholder('Contoh: 001, 123, dll')
                    ->helperText('Nomor peserta yang menang'),
            ])
            ->columns(2)
            ->columnSpanFull()
            ->visible(fn($record) => $record && $record->status === 'ended');
    }
}
