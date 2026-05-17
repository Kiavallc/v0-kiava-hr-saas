<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\EmployeeProfile;
use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('auth.login'));
    }

    public function test_dashboard_shows_employee_statistics()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'hr_admin',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }
}
