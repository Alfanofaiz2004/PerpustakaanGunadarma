<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function view(User $user, Loan $loan): bool
    {
        return $user->isAdmin() || $loan->user_id === $user->id;
    }

    public function returnBook(User $user, Loan $loan): bool
    {
        return $loan->user_id === $user->id && $loan->status === 'dipinjam';
    }
}
