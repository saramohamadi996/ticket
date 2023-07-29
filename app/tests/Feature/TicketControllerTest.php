<?php

namespace Tests\Feature;

use App\Http\Controllers\TicketController;
use App\Http\Middleware\Authenticate;
use App\Policies\TicketPolicy;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\TicketRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use App\Models\Ticket;
use App\Models\User;
use Mockery;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }


    public function testAuthenticatedUserCanAccessIndex()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        Sanctum::actingAs($employee);
        Ticket::factory()->count(5)->create();
        $response = $this->get('/api/tickets');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user',
                    'employee',
                    'title',
                    'content',
                    'status',
                    'priority',
                ],
            ],
        ]);
    }

    public function testUnauthenticatedUserCannotAccessIndex()
    {
        $this->withoutMiddleware(Authenticate::class);
        $response = $this->get('/api/tickets');
        $response->assertStatus(403);
    }

    public function testAuthenticatedUserWithPermissionCanAccessTicket()
    {
        $adminRole = Role::findOrCreate('admin');
        $employeeRole = Role::findOrCreate('employee');
        $viewTicketPermission = Permission::findOrCreate('view tickets');
        $user = User::factory()->create();
        if (rand(0, 1)) {
            $user->assignRole($adminRole);
        } else {
            $user->assignRole($employeeRole);
        }
        $user->givePermissionTo($viewTicketPermission);
        $ticket = Ticket::factory()->create();
        $this->actingAs($user);
        $this->withoutMiddleware(Authenticate::class);

        $response = $this->get('/api/tickets/' . $ticket->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'employee',
                'title',
                'content',
                'status',
                'priority',
            ],
        ]);
        $response->assertJson([
            'data' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'content' => $ticket->content,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
            ],
        ]);
    }

    public function testAuthenticatedUserWithCustomerOrEmployeeRoleCanCreateTicket()
    {
        $this->withoutMiddleware(Authenticate::class);

        $customerRole = Role::findOrCreate('customer');
        $employeeRole = Role::findOrCreate('employee');
        $createTicketPermission = Permission::findOrCreate('create tickets');

        $user = User::factory()->create();
        $user->givePermissionTo($createTicketPermission);

        if (rand(0, 1)) {
            $user->assignRole($customerRole);
        } else {
            $user->assignRole($employeeRole);
        }

        $mockedRepository = Mockery::mock(TicketRepository::class);
        $mockedRepository->shouldReceive('assignTicketToEmployee')->andReturn($employee = User::factory()->create());

        $this->app->instance(TicketRepositoryInterface::class, $mockedRepository);

        Sanctum::actingAs($user);

        $ticketData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'status' => 'open',
            'priority' => 'high',
        ];

        $response = $this->post('/api/tickets', $ticketData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'content',
                'status',
                'priority',
                'employee_id',
            ],
            'message',
            'assigned_employee',
        ]);

        $response->assertJson([
            'data' => [
                'user_id' => $user->id,
                'title' => $ticketData['title'],
                'content' => $ticketData['content'],
                'status' => $ticketData['status'],
                'priority' => $ticketData['priority'],
            ],
            'assigned_employee' => true,
        ]);
    }
    public function testUserCanUpdateOwnTicket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        $policy = new TicketPolicy();
        $this->assertTrue($policy->update($user, $ticket));
    }

    public function testUserWithEmployeeRoleCanUpdateTicket()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $ticket = Ticket::factory()->create();
        $policy = new TicketPolicy();
        $this->assertTrue($policy->update($user, $ticket));
    }

    public function testUserCannotUpdateOtherUserTicket()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user1->id]);
        $policy = new TicketPolicy();
        $this->assertFalse($policy->update($user2, $ticket));
    }


    public function testUserCanDeleteOwnTicket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        $policy = new TicketPolicy();
        $this->assertTrue($policy->delete($user, $ticket));
    }

    public function testUserWithEmployeeRoleCanDeleteTicket()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $ticket = Ticket::factory()->create();
        $policy = new TicketPolicy();
        $this->assertTrue($policy->delete($user, $ticket));
    }

    public function testUserCannotDeleteOtherUserTicket()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user1->id]);
        $policy = new TicketPolicy();
        $this->assertFalse($policy->delete($user2, $ticket));
    }
}
