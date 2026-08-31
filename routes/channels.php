<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Channel untuk user notifications
 * Hanya user yang bersangkutan yang bisa listen
 */
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Channel untuk booking notifications (barber & customer)
 */
Broadcast::channel('booking.{bookingId}', function ($user, $bookingId) {
    $booking = \App\Models\Booking::find($bookingId);
    
    if (!$booking) {
        return false;
    }
    
    // Only barber dan customer dari booking ini bisa listen
    return $user->id === $booking->customer_id || 
           ($user->barber && $user->barber->id === $booking->barber_id);
});
