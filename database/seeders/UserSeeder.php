<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        User::create([
            'company_id' => $company->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@kiava.local',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'is_active' => true,
            'force_password_change' => false,
            'mfa_enabled' => false,
        ]);

        User::create([
            'company_id' => $company->id,
            'first_name' => 'Employee',
            'last_name' => 'Test',
            'email' => 'employee@kiava.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true,
            'force_password_change' => false,
            'mfa_enabled' => false,
        ]);
    }
}
