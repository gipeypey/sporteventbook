<?php

namespace App\Interfaces;

use App\Models\Event;

interface BookingRepositoryInterface
{
    public function getAllBookings(
        ?string $search,
        ?int $eventId,
        ?string $status,
        ?string $email,
        ?int $limit,
    );

    public function getByCode(
        string $code
    );

    public function getByCodeAndEmail(
        string $code,
        string $email
    );

    public function saveInformation(
        array $data
    );

    public function createBooking(
        Event $event
    );
}
