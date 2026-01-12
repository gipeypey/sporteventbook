<?php

namespace App\Filament\Resources\BookingResource\Tables;

use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class BookingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Booking Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        'expired' => 'gray',
                        'canceled' => 'gray',
                        'unknown' => 'gray',
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'canceled' => 'Canceled',
                        'unknown' => 'Unknown',
                        'completed' => 'Completed',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_checked_in')
                    ->label('Checked In')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('checked_in_at')
                    ->label('Check-in Time')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Not checked in')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'canceled' => 'Canceled',
                        'unknown' => 'Unknown',
                        'completed' => 'Completed',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('is_checked_in')
                    ->label('Check-in Status')
                    ->options([
                        true => 'Checked In',
                        false => 'Not Checked In',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === true) {
                            return $query->where('is_checked_in', true);
                        } elseif ($state['value'] === false) {
                            return $query->where('is_checked_in', false);
                        }
                    }),
                Tables\Filters\SelectFilter::make('event_id')
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
                    ->searchable()
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil-square'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
