<?php

namespace App\Filament\Resources\Withdrawals\Schemas;

use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class WithdrawalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            static::getWithdrawalRequestSection(),
            static::getBankDetailsSection(),
            static::getAdminSection(),
        ]);
    }

    private static function getWithdrawalRequestSection(): Section
    {
        return Section::make('Informasi Penarikan')
            ->description('Detail jumlah penarikan dana')
            ->icon('heroicon-o-banknotes')
            ->schema([
                Forms\Components\Placeholder::make('available_balance')
                    ->label('Saldo Tersedia')
                    ->content(function () {
                        $user = auth()->user();
                        if ($user && $user->isVenueOwner()) {
                            $balance = $user->getAvailableBalance();
                            return 'Rp ' . number_format($balance, 0, ',', '.');
                        }
                        return 'Rp 0';
                    })
                    ->visible(fn() => auth()->user()?->isVenueOwner()),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Penarikan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $amount = (float) $state;
                            $commission = Withdrawal::calculateCommission($amount);
                            $netAmount = Withdrawal::calculateNetAmount($amount);
                            $set('commission', $commission);
                            $set('net_amount', $netAmount);
                        }
                    })
                    ->rules([
                        fn() => function (string $attribute, $value, \Closure $fail) {
                            $user = auth()->user();
                            if ($user && $user->isVenueOwner()) {
                                $availableBalance = $user->getAvailableBalance();
                                if ((float) $value > $availableBalance) {
                                    $fail('Jumlah penarikan melebihi saldo yang tersedia (Rp ' . number_format($availableBalance, 0, ',', '.') . ')');
                                }
                            }
                        },
                    ])
                    ->disabled(fn($record) => $record && !$record->isPending()),

                Forms\Components\TextInput::make('commission')
                    ->label('Komisi Platform (2%)')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->placeholder('0'),

                Forms\Components\TextInput::make('net_amount')
                    ->label('Jumlah Diterima')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->placeholder('0')
                    ->helperText('Jumlah yang akan ditransfer ke rekening Anda'),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getBankDetailsSection(): Section
    {
        return Section::make('Informasi Rekening')
            ->description('Detail rekening bank untuk transfer')
            ->icon('heroicon-o-building-library')
            ->schema([
                Forms\Components\TextInput::make('bank_name')
                    ->label('Nama Bank')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: BCA, Mandiri, BNI')
                    ->disabled(fn($record) => $record && !$record->isPending()),

                Forms\Components\TextInput::make('account_number')
                    ->label('Nomor Rekening')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nomor rekening')
                    ->disabled(fn($record) => $record && !$record->isPending()),

                Forms\Components\TextInput::make('account_holder_name')
                    ->label('Atas Nama')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Nama pemilik rekening')
                    ->disabled(fn($record) => $record && !$record->isPending()),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    private static function getAdminSection(): Section
    {
        return Section::make('Admin Action')
            ->description('Approval dan bukti transfer')
            ->icon('heroicon-o-shield-check')
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ])
                    ->required()
                    ->native(false)
                    ->disabled(fn() => auth()->user()?->isVenueOwner()),

                Forms\Components\FileUpload::make('proof_of_transfer')
                    ->label('Bukti Transfer')
                    ->image()
                    ->directory('proof-of-transfers')
                    ->visibility('private')
                    ->helperText('Upload bukti transfer untuk menyelesaikan penarikan')
                    ->disabled(fn() => auth()->user()?->isVenueOwner()),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->placeholder('Catatan untuk venue owner (opsional)')
                    ->disabled(fn() => auth()->user()?->isVenueOwner())
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->columnSpanFull()
            ->visible(fn($record) => $record || auth()->user()?->isAdmin());
    }
}
