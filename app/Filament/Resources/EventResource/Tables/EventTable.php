<?php

namespace App\Filament\Resources\EventResource\Tables;

use App\Models\EventCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class EventTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->size(50),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('venue.name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'warning',
                        'ended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'open' => 'Buka Pendaftaran',
                        'closed' => 'Pendaftaran Ditutup',
                        'ended' => 'Event Selesai',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Maks Peserta')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_prize')
                    ->label('Total Hadiah')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('winner_name')
                    ->label('Pemenang')
                    ->placeholder('Belum ada pemenang')
                    ->badge()
                    ->color(fn($state) => filled($state) ? 'success' : 'gray')
                    ->formatStateUsing(fn($record) => $record->winner_name ? ($record->winner_number ? $record->winner_name . ' • #' . $record->winner_number : $record->winner_name) : 'Belum ada pemenang')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Buka Pendaftaran',
                        'closed' => 'Pendaftaran Ditutup',
                        'ended' => 'Event Selesai',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Event Unggulan')
                    ->placeholder('Semua event')
                    ->trueLabel('Hanya unggulan')
                    ->falseLabel('Bukan unggulan'),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(EventCategory::all()->pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
