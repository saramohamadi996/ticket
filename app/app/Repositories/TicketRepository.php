<?php

namespace App\Repositories;

use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\User;

class TicketRepository implements TicketRepositoryInterface
{
    /**
     * Assigns a ticket to an employee.
     *
     * @param Ticket $ticket
     * @return User|null
     */
    public function assignTicketToEmployee(Ticket $ticket): ?User
    {
        $employee = User::role('employee')
            ->withCount('tickets')
            ->orderBy('tickets_count', 'asc')
            ->first();

        if (!$employee) {
            return null;
        }

        $ticket->employee_id = $employee->id;
        $ticket->save();
        return $employee;
    }
}
