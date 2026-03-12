<?php

namespace App\Filament\Actions;

use App\Exports\BookingExport;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportBookingsAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'exportBookings';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::ArrowDownTray);
        $this->label('Export to Excel');
        $this->color('success');
        $this->size(ActionSize::Large);

        $this->action(function (array $data): \Symfony\Component\HttpFoundation\BinaryFileResponse {
            $filters = [
                'event_id' => $data['event_id'] ?? null,
                'payment_status' => $data['payment_status'] ?? null,
                'is_checked_in' => $data['is_checked_in'] ?? null,
                'date_from' => $data['date_from'] ?? null,
                'date_to' => $data['date_to'] ?? null,
                'search' => $data['search'] ?? null,
            ];

            $filename = 'bookings-' . now()->format('Y-m-d-His') . '.xlsx';

            return Excel::download(new BookingExport($filters), $filename);
        });

        $this->modalHeading('Export Bookings');
        $this->modalDescription('Select your export preferences below');

        $this->schema([
            \Filament\Forms\Components\Select::make('event_id')
                ->label('Filter by Event')
                ->placeholder('All Events')
                ->searchable()
                ->preload()
                ->options(function () {
                    $user = auth()->user();
                    
                    if ($user && $user->isVenueOwner()) {
                        return \App\Models\Event::whereHas('venue', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })->pluck('title', 'id');
                    }
                    
                    return \App\Models\Event::pluck('title', 'id');
                }),
            
            \Filament\Forms\Components\Select::make('payment_status')
                ->label('Filter by Payment Status')
                ->placeholder('All Statuses')
                ->options(\App\Enums\PaymentStatus::options()),
            
            \Filament\Forms\Components\Select::make('is_checked_in')
                ->label('Filter by Check-in Status')
                ->placeholder('All')
                ->options([
                    true => 'Checked In',
                    false => 'Not Checked In',
                ]),
            
            \Filament\Forms\Components\DatePicker::make('date_from')
                ->label('Bookings From')
                ->native(false),
            
            \Filament\Forms\Components\DatePicker::make('date_to')
                ->label('Bookings To')
                ->native(false),
            
            \Filament\Forms\Components\TextInput::make('search')
                ->label('Search')
                ->placeholder('Search by name, email, phone, or booking code')
                ->columnSpanFull(),
        ]);

        $this->modalSubmitActionLabel('Export');
        $this->modalCancelActionLabel('Cancel');

        $this->successNotificationTitle('Bookings exported successfully');
    }
}
