<?php

namespace App\Policies;

use App\Models\Ticket;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tickets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function ticketHistory(User $user,Ticket $ticket)
    {
        return $user->hasPermission('Ticket','ticketHistory');
    }

    public function ticketView(User $user,Ticket $ticket)
    {
        return $user->hasPermission('Ticket','ticketView');
    }

    public function ticketAssign(User $user,Ticket $ticket)
    {
        return $user->hasPermission('Ticket','ticketAssign');
    }

    public function ticketSolver(User $user,Ticket $ticket)
    {
        return $user->hasPermission('Ticket','ticketSolver');
    }

    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the ticket.
     *
     * @param  \App\User  $user
     * @param  \App\Ticket  $ticket
     * @return mixed
     */
    public function view(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can create tickets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the ticket.
     *
     * @param  \App\User  $user
     * @param  \App\Ticket  $ticket
     * @return mixed
     */
    public function update(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can delete the ticket.
     *
     * @param  \App\User  $user
     * @param  \App\Ticket  $ticket
     * @return mixed
     */
    public function delete(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can restore the ticket.
     *
     * @param  \App\User  $user
     * @param  \App\Ticket  $ticket
     * @return mixed
     */
    public function restore(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the ticket.
     *
     * @param  \App\User  $user
     * @param  \App\Ticket  $ticket
     * @return mixed
     */
    public function forceDelete(User $user, Ticket $ticket)
    {
        //
    }
}
