<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        $user->campaigns()->delete();
        $user->blogPosts()->delete();
        $user->pages()->delete();
        $user->uploadedMedia()->delete();

        $user->auditLogs()->delete();

        $user->verifiedDonations()->update([
            'verified_by' => null,
        ]);

        $user->roles()->detach();
        $user->permissions()->detach();
    }
}
