<?php

namespace App\Repositories\Interfaces;

use App\Models\Ticket;
use App\Models\User;

interface TicketRepositoryInterface
{
    /**
     * Assigns a ticket to an employee.
     *
     * @param Ticket $ticket
     * @return User|null
     */
    public function assignTicketToEmployee(Ticket $ticket): ?User;
}
