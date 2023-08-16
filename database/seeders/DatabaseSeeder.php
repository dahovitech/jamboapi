<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $user = \App\Models\User::create([
            'name' => "Admin",
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin')
        ]);

        $role = Role::create(['name' => 'super_admin']);

        $user->assignRole($role);

    }
}
