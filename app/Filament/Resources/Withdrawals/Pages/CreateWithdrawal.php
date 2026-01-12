<?php

namespace App\Filament\Resources\Withdrawals\Pages;

use App\Filament\Resources\Withdrawals\WithdrawalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWithdrawal extends CreateRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-fill user_id from authenticated user
        $data['user_id'] = auth()->id();

        // Set default status to pending
        $data['status'] = 'pending';

        // Set requested_at timestamp
        $data['requested_at'] = now();

        return $data;
    }
}
