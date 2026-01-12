<?php

namespace App\Filament\Resources\Withdrawals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class WithdrawalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Venue Owner')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => auth()->user()?->isAdmin()),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah Penarikan')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission')
                    ->label('Komisi (2%)')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Jumlah Diterima')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('No. Rekening')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('account_holder_name')
                    ->label('Atas Nama')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'primary' => 'completed',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Tanggal Request')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Approve')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tanggal Selesai')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
            ])
            ->defaultSort('requested_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn() => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->isAdmin()),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                // Venue owner hanya bisa lihat withdrawal mereka sendiri
                if (auth()->user()?->isVenueOwner()) {
                    $query->where('user_id', auth()->id());
                }
                return $query;
            });
    }
}
