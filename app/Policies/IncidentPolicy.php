<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Incident $incident): bool
    {
        return $user->isAdmin() || (int) $incident->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->isAdmin() || (int) $incident->user_id === (int) $user->id;
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->isAdmin();
    }

    public function deleteAttachment(User $user, IncidentAttachment $attachment): bool
    {
        return $user->isAdmin() || (int) $attachment->uploaded_by === (int) $user->id;
    }
}
