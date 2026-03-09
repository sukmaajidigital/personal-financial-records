<?php

namespace App\Policies;

use App\Models\PlannedTransaction;
use App\Models\User;

class PlannedTransactionPolicy
{
    public function update(User $user, PlannedTransaction $plannedTransaction): bool
    {
        return $user->id === $plannedTransaction->user_id && $plannedTransaction->isDraft();
    }

    public function delete(User $user, PlannedTransaction $plannedTransaction): bool
    {
        return $user->id === $plannedTransaction->user_id && $plannedTransaction->isDraft();
    }

    public function post(User $user, PlannedTransaction $plannedTransaction): bool
    {
        return $user->id === $plannedTransaction->user_id && $plannedTransaction->isDraft();
    }
}
