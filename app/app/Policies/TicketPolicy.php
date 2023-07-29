<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'employee']);
    }

    public function view(User $user, Ticket $ticket)
    {
        return $user->hasAnyRole(['admin', 'employee']);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['customer', 'employee']);
    }

    public function update(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id || $user->hasRole('employee');
    }

    public function delete(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id || $user->hasRole('employee');
    }

    public function replyToTicket(User $user, Ticket $ticket)
    {
        return $user->hasRole('employee');
    }
}
