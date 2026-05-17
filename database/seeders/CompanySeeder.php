<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Kiava Demo Company',
            'slug' => 'kiava-demo',
            'domain' => 'demo.local',
            'description' => 'Demo company for testing',
            'employee_limit' => 1000,
            'storage_limit_gb' => 1000,
            'status' => 'active',
            'is_active' => true,
        ]);
    }
}
