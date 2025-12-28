<?php

namespace App\Policies;

use App\Models\Reservations;
use App\Models\User;

class ReservationsPolicy
{
    public function update(User $user, Reservations $reservation)
    {
        return $user->id === $reservation->user_id;
    }

    public function cancel(User $user, Reservations $reservation)
    {
        return $user->id === $reservation->user_id;
    }

    public function approve(User $user, Reservations $reservation)
    {
        return $user->id === $reservation->apartment->owner_id;
    }
}
