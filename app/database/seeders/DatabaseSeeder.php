<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPermissions();
        $this->createRoles();
        $this->createAdminUser();
        Ticket::factory()->count(5)->create();
    }
    private function createPermissions()
    {
        $permissions = [
            'create users',
            'view users',
            'edit users',
            'delete users',
            'reply-to-ticket',
            'view tickets',
            'create tickets',
            'update ticket',
        ];

            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission);
            }
    }

    private function createRoles()
    {
        $adminRole = Role::findOrCreate('admin');
        $permissions = Permission::pluck('name')->toArray();
        $adminRole->syncPermissions($permissions);

        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeePermissions = [
            'view tickets',
            'create tickets',
            'reply-to-ticket',
            'update ticket',
        ];
        $employeeRole->syncPermissions($employeePermissions);

        $customerRole = Role::findOrCreate('customer');
        $customerRole->syncPermissions([]);
    }

    private function createAdminUser()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);

        $adminRole = Role::findByName('admin');
        $admin->assignRole($adminRole);
    }
}
