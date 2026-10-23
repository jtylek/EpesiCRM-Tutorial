<?php

namespace Custom\Modules\Tutorial\Policies;

use App\Models\User;
use Custom\Modules\Tutorial\Models\TutorialTicket;

/** Staff can read/create; creators and managers can change records. */
class TutorialTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'employee']);
    }

    public function view(User $user, TutorialTicket $record): bool
    {
        return $this->viewAny($user) && $record->newQuery()->whereKey($record->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, TutorialTicket $record): bool
    {
        return $this->view($user, $record)
            && ($user->hasAnyRole(['super_admin', 'manager']) || $record->created_by === $user->id);
    }

    public function delete(User $user, TutorialTicket $record): bool
    {
        return $this->update($user, $record);
    }

    public function restore(User $user, TutorialTicket $record): bool
    {
        return $this->viewAny($user)
            && ($user->hasAnyRole(['super_admin', 'manager']) || $record->created_by === $user->id);
    }

    public function forceDelete(User $user, TutorialTicket $record): bool
    {
        return $user->hasRole('super_admin');
    }
}
