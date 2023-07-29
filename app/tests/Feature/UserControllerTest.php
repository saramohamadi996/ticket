<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function testAdminCanViewUser()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        User::factory()->count(10)->create();
        $viewResponse = $this->get("/api/users");

        $viewResponse->assertStatus(200);
        $viewResponse->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }

    public function testUnauthenticatedAdminCannotAccessIndex()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->get('/api/users');
        $response->assertStatus(403);
    }

    public function testAdminCanCreateUser()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'test1234',
        ];
        $response = $this->post('/api/users', $userData);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
            'message',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
    }

    public function testUnauthorizedUserCannotCreateUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'test5678',
        ];
        $response = $this->post('/api/users', $userData);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);
    }

    public function testAdminCanUpdateUser()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        $userToUpdate = User::factory()->create();
        $userData = [
            'name' => 'Updated User Name',
            'password' => 'updated1234',
        ];
        $response = $this->put("/api/users/{$userToUpdate->id}", $userData);
        $response->assertStatus(200);
        $userToUpdate->refresh();
        $this->assertEquals('Updated User Name', $userToUpdate->name);
        $this->assertTrue(Hash::check('updated1234', $userToUpdate->password));
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
            'message',
        ]);
        $response->assertJson([
            'data' => [
                'id' => $userToUpdate->id,
                'name' => 'Updated User Name',
                'email' => $userToUpdate->email,
            ],
            'message' => 'User updated successfully',
        ]);
    }

    public function testAdminCanDeleteUser()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        $userToDelete = User::factory()->create();
        $response = $this->delete("/api/users/{$userToDelete->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User deleted successfully',
        ]);
    }

}

