<?php

namespace Database\Seeders\User;

use App\Enums\User\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->command->info('Creating Admin User...');

        try {

            $user = new User();
            $user->username = 'admin';
            $user->name = 'Mohamed Hassan';
            $user->email = 'admin@admin.com';
            $user->password = 'Mans123456';
            $user->is_active = UserStatus::ACTIVE;
            $user->email_verified_at = now();
            $user->phone = '1234567890';
            $user->address = 'Admin Address';
            $user->save();

            $role = Role::where('name', 'super admin')->first();

            $user->assignRole($role);

        } catch (\Exception $e) {
            $this->command->error('Error creating user: ' . $e->getMessage());
            return;
        }

    }
}
