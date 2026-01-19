<?php

namespace App\Policies;

use App\Models\Apartment;
use App\Models\User;
use App\Models\Reservations;
use Illuminate\Auth\Access\Response;

class ApartmentPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->type, ['owner', 'admin']);
    }
    public function destroy(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->owner_id || $user->type === 'admin';
    }
    public function update(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->owner_id || $user->type === 'admin';
    }
    public function showId(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->owner_id || $user->type === 'admin';
    }
    public function showUser(User $user, User $targetUser): bool
    {
        // return $user->id === $targetUser->id || $user->type === 'admin';
        return true;
    }
    public function rent(User $user, Apartment $apartment): bool
    {
        return $user->type === 'tenant' && $apartment->status === 'available';
    }

    public function canRate(User $user, Apartment $apartment): Response
    {
        $q = Reservations::query()
            ->where('user_id', $user->id)
            ->where('apartment_id', $apartment->id);
        if (!$q->exists()) {
            return Response::deny('You have not reserved this apartment before.');
        }
        if (!$q->where('status', 'confirmed')) {
            return Response::deny('You can only rate after a confirmed reservation.');
        }
        if (!$q->where('end_date', '<=', now())->exists()) {
            return Response::deny('You can only rate after finishing your stay.');
        }
        return Response::allow();
    }
}
