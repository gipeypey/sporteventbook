<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use UnitEnum;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static UnitEnum|string|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Promo Code Information')
                    ->description('Basic information about the promo code')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Promo Code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., RUNNER10, EARLYBIRD20')
                            ->helperText('This code will be entered by users at checkout')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Promo Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Early Bird Discount')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(65535)
                            ->placeholder('Describe this promo code')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Discount Settings')
                    ->description('Configure discount value and type')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount (Rp)',
                            ])
                            ->required()
                            ->default('percentage')
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('value')
                            ->label('Discount Value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn (callable $get) => $get('type') === 'percentage' ? '%' : 'Rp')
                            ->helperText(fn (callable $get) => $get('type') === 'percentage' 
                                ? 'Enter percentage value (e.g., 10 for 10%)' 
                                : 'Enter fixed amount in Rupiah (e.g., 50000 for Rp 50,000)')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('minimum_amount')
                            ->label('Minimum Order Amount')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->helperText('Leave empty for no minimum')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Usage Limits')
                    ->description('Control how many times this code can be used')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Usage Limit')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Unlimited')
                            ->helperText('Leave empty for unlimited usage')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('used_count')
                            ->label('Times Used')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically incremented when used')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Validity Period')
                    ->description('Set when this promo code is active')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Start Date')
                            ->native(false)
                            ->displayFormat('d M Y H:i')
                            ->helperText('Leave empty for immediate activation')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiry Date')
                            ->native(false)
                            ->displayFormat('d M Y H:i')
                            ->helperText('Leave empty for no expiry')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Uncheck to temporarily disable this code')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'success',
                        'fixed' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => 
                        $record->type === 'percentage' 
                            ? "{$record->value}%" 
                            : 'Rp ' . number_format($record->value, 0, ',', '.')
                    ),
                Tables\Columns\TextColumn::make('minimum_amount')
                    ->label('Min. Amount')
                    ->sortable()
                    ->money('IDR')
                    ->placeholder('None'),
                Tables\Columns\TextColumn::make('usage')
                    ->label('Usage')
                    ->formatStateUsing(fn ($record) => 
                        $record->usage_limit 
                            ? "{$record->used_count}/{$record->usage_limit}" 
                            : "{$record->used_count}/∞"
                    )
                    ->badge()
                    ->color(fn ($record) => 
                        $record->usage_limit && $record->used_count >= $record->usage_limit 
                            ? 'danger' 
                            : 'success'
                    ),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Never')
                    ->color(fn ($record) => 
                        $record->expires_at && $record->expires_at->isPast() 
                            ? 'danger' 
                            : null
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
                Tables\Filters\Filter::make('expired')
                    ->query(fn ($query) => $query->where('expires_at', '<', now())),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
