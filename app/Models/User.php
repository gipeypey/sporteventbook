<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVenueOwner(): bool
    {
        return $this->role === 'venue_owner';
    }

    /**
     * Calculate available balance for venue owner
     * Based on checked-in bookings minus withdrawn amounts
     */
    public function getAvailableBalance(): float
    {
        if (!$this->isVenueOwner()) {
            return 0;
        }

        // Get total from checked-in bookings for this venue owner's events
        $totalRevenue = Booking::whereHas('event.venue', function ($query) {
            $query->where('user_id', $this->id);
        })
            ->where('is_checked_in', true)
            ->where('payment_status', 'success')
            ->sum('total');

        // Get total withdrawn (approved or completed)
        $totalWithdrawn = $this->withdrawals()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        return max(0, $totalRevenue - $totalWithdrawn);
    }
}
