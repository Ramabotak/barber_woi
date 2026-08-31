<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Admin dapat melihat semua booking.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin dapat melihat detail booking apa pun.
     * Barber hanya bisa lihat booking miliknya.
     * Customer hanya bisa lihat booking miliknya.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isBarber() && $user->barber) {
            return $booking->barber_id === $user->barber->id;
        }

        if ($user->isCustomer()) {
            return $booking->customer_id === $user->id;
        }

        return false;
    }

    /**
     * Admin dapat mengupdate/cancel booking apa pun.
     * Barber dapat update booking miliknya.
     */
    public function update(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isBarber() && $user->barber) {
            return $booking->barber_id === $user->barber->id;
        }

        return false;
    }

    /**
     * Hanya admin yang bisa force complete atau cancel booking.
     */
    public function forceActions(User $user): bool
    {
        return $user->isAdmin();
    }
}
