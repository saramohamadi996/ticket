<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Request\Ticket\CreateTicketRequest;
use App\Http\Request\Ticket\UpdateTicketRequest;
use App\Notifications\TicketReplyNotification;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use App\Models\User;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Retrieves a paginated list of tickets with associated users and employees.
     *
     * @return AnonymousResourceCollection The paginated ticket list.
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::with(['user', 'employee'])->paginate(10);
        return TicketResource::collection($tickets);
    }

    /**
     * Retrieves a single ticket and its details.
     *
     * @param Ticket $ticket The ticket to be retrieved.
     * @return TicketResource The ticket resource.
     * @throws AuthorizationException
     */
    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);
        return new TicketResource($ticket);
    }

    /**
     * Creates a new ticket and assigns it to an employee.
     *
     * @param CreateTicketRequest $request The request containing ticket details.
     * @return JsonResponse The JSON response with the created ticket data and assigned employee.
     * @throws AuthorizationException
     */
    public function store(CreateTicketRequest $request): JsonResponse
    {
        $this->authorize('create', Ticket::class);
        $user = Auth::user();
        $ticket = new Ticket([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);
        $ticket->save();
        $employee = $this->ticketRepository->assignTicketToEmployee($ticket);
        return response()->json([
            'data' => new TicketResource($ticket),
            'message' => trans('Ticket created successfully'),
            'assigned_employee' => $employee
        ], 201);
    }

    /**
     * Updates a ticket with a new reply and notifies relevant users.
     *
     * @param UpdateTicketRequest $request The request containing the reply content.
     * @param Ticket $ticket The ticket to be updated.
     * @return TicketResource The updated ticket resource.
     * @throws AuthorizationException
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource
    {
        $this->authorize('update', $ticket);

        $ticket->content .= "\n\nReply: " . $request->content;
        $ticket->save();

        $users = User::role(['admin', 'employee'])->get();
        foreach ($users as $user) {
            $user->notify(new TicketReplyNotification($ticket, $request->content));
        }

        return new TicketResource($ticket);
    }

    /**
     * Deletes a ticket.
     *
     * @param Ticket $ticket The ticket to be deleted.
     * @return JsonResponse The JSON response indicating the ticket was deleted successfully.
     * @throws AuthorizationException
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();
        return response()->json(['message' => trans('Ticket deleted successfully')]);
    }
}
