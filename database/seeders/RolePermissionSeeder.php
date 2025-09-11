<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Repositories\UserRepository;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $userRepository = app(UserRepository::class);

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $managers = [
            ['name' => 'Manager', 'email' => 'manager@example.com'],
            ['name' => 'Ziad Mohamed', 'email' => 'ziad.manager@example.com'],
        ];

        foreach ($managers as $managerData) {
            $manager = $userRepository->findByEmail($managerData['email']) ?? $userRepository->create([
                'name' => $managerData['name'],
                'email' => $managerData['email'],
                'password' => 'password',
            ]);
            $manager->assignRole('manager');
        }

        $users = [
            ['name' => 'Jhon Doe', 'email' => 'user@example.com'],
            ['name' => 'Ziad Mohamed', 'email' => 'ziad.developer@example.com'],
        ];

        foreach ($users as $userData) {
            $user = $userRepository->findByEmail($userData['email']) ?? $userRepository->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => 'password',
            ]);
            $user->assignRole('user');
        }
    }
}
