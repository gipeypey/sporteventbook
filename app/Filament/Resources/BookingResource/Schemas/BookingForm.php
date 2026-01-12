<?php

namespace App\Filament\Resources\BookingResource\Schemas;

use App\Models\Event;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            static::getBookingInformationSection(),
            static::getPaymentDetailsSection(),
            static::getCheckInSection(),
        ]);
    }

    private static function getBookingInformationSection(): Section
    {
        return Section::make('Informasi Booking')
            ->description('Detail event dan peserta')
            ->icon('heroicon-o-ticket')
            ->schema([
                static::getEventSelectField(),
                ...static::getParticipantFields(),
                static::getPaymentStatusSelectField(),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getEventSelectField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('event_id')
            ->label('Event')
            ->options(function () {
                $user = auth()->user();

                if ($user && $user->isVenueOwner()) {
                    return Event::whereHas('venue', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->pluck('title', 'id');
                }

                return Event::all()->pluck('title', 'id');
            })
            ->required()
            ->searchable()
            ->preload()
            ->placeholder('Pilih event')
            ->disabled(fn() => auth()->user()?->isVenueOwner())
            ->columnSpanFull();
    }

    private static function getParticipantFields(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Nama Peserta')
                ->required()
                ->maxLength(255)
                ->placeholder('Masukkan nama lengkap peserta')
                ->disabled(fn() => auth()->user()?->isVenueOwner()),
            Forms\Components\TextInput::make('email')
                ->label('Alamat Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->placeholder('Masukkan alamat email')
                ->disabled(fn() => auth()->user()?->isVenueOwner()),
            Forms\Components\TextInput::make('phone')
                ->label('Nomor Telepon')
                ->tel()
                ->required()
                ->maxLength(255)
                ->placeholder('Masukkan nomor telepon')
                ->disabled(fn() => auth()->user()?->isVenueOwner()),
        ];
    }

    private static function getPaymentStatusSelectField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('payment_status')
            ->label('Status Pembayaran')
            ->options(static::getPaymentStatusOptions())
            ->default('pending')
            ->required()
            ->native(false);
    }

    private static function getPaymentStatusOptions(): array
    {
        return [
            'pending' => 'Menunggu Pembayaran',
            'success' => 'Pembayaran Terkonfirmasi',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            'canceled' => 'Dibatalkan',
            'unknown' => 'Unknown',
            'completed' => 'Event Selesai',
        ];
    }

    private static function getPaymentDetailsSection(): Section
    {
        return Section::make('Detail Pembayaran')
            ->description('Informasi keuangan dan rincian harga')
            ->icon('heroicon-o-banknotes')
            ->schema(static::getPaymentFields())
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getPaymentFields(): array
    {
        return [
            Forms\Components\TextInput::make('subtotal')
                ->label('Subtotal')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->required()
                ->prefix('Rp')
                ->placeholder('0')
                ->disabled(fn() => auth()->user()?->isVenueOwner()),
            Forms\Components\TextInput::make('tax')
                ->label('Pajak (11%)')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->required()
                ->prefix('Rp')
                ->placeholder('0')
                ->disabled(fn() => auth()->user()?->isVenueOwner()),
            Forms\Components\TextInput::make('insurance')
                ->label('Asuransi')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->required()
                ->prefix('Rp')
                ->placeholder('0')
                ->disabled(fn() => auth()->user()?->isVenueOwner()),
            Forms\Components\TextInput::make('total')
                ->label('Total Pembayaran')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->required()
                ->prefix('Rp')
                ->placeholder('0')
                ->disabled()
                ->dehydrated(),
        ];
    }

    private static function getCheckInSection(): Section
    {
        return Section::make('Informasi Check-In')
            ->description('Status dan waktu check-in peserta')
            ->icon('heroicon-o-qr-code')
            ->schema([
                Forms\Components\Toggle::make('is_checked_in')
                    ->label('Sudah Check-In')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Check-in dilakukan melalui Ticket Scanner'),
                Forms\Components\DateTimePicker::make('checked_in_at')
                    ->label('Waktu Check-In')
                    ->disabled()
                    ->dehydrated()
                    ->displayFormat('d F Y, H:i')
                    ->helperText('Waktu otomatis tersimpan saat scan ticket'),
            ])
            ->columns(2)
            ->columnSpanFull()
            ->hidden(fn($record) => $record === null);
    }
}
