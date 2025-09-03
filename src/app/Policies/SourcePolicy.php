<?php

namespace App\Policies;

use App\Models\Source;
use App\Models\User;

class SourcePolicy
{
    public function update(User $user, Source $source): bool
    {
        return $user->id === $source->user_id;
    }

    public function delete(User $user, Source $source): bool
    {
        return $user->id === $source->user_id;
    }
}
