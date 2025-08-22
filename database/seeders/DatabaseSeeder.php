<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\User;
use App\Services\Helpers\Helper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        # Create default SuperAdmin user
        $user = User::where('email','admin@admin.com')->firstOrNew([
            'name' => 'Administrator',
            'email' => 'admin@admin.com'
        ]);

        $user->phone = Helper::phoneFormatDB('998901234567');
        $user->name = "Administrator";
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('admin@admin.com');
        $user->is_admin = true;
        $user->save();
        $role = Role::where('name', 'Super Admin')->first();
        if (!$role) {
            $role = Role::create(['name' => 'Super Admin']);
        }
        $user->assignRole($role);
        $this->command->info('Super Admin user created successfully!');
    }
}
