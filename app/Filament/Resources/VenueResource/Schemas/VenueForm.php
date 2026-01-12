<?php

namespace App\Filament\Resources\VenueResource\Schemas;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VenueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Venue')
                    ->description('Basic information about the venue')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Select::make('user_id')
                            ->label('Venue Owner')
                            ->options(User::where('role', 'venue_owner')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn() => auth()->user()?->isAdmin())
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state ?? ''));
                            })
                            ->columnSpanFull(),
                        Textarea::make('address')
                            ->label('Address')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
