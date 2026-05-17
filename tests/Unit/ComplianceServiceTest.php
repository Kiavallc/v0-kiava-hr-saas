<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\DocumentRequirement;
use App\Models\EmployeeProfile;
use App\Models\User;
use Tests\TestCase;

class ComplianceServiceTest extends TestCase
{
    public function test_compliance_percentage_calculation()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $employee = EmployeeProfile::factory()->create(['user_id' => $user->id]);

        $requirement1 = DocumentRequirement::factory()->create(['company_id' => $company->id]);
        $requirement2 = DocumentRequirement::factory()->create(['company_id' => $company->id]);

        // Employee has 1 of 2 required documents
        $employee->documents()->create([
            'document_requirement_id' => $requirement1->id,
            'status' => 'approved',
            'expiration_date' => now()->addYear(),
        ]);

        // Compliance should be 50%
        $compliance = ($employee->documents()->where('status', 'approved')->count() / 
                      $company->documentRequirements->count()) * 100;

        $this->assertEquals(50, $compliance);
    }

    public function test_expired_documents_not_counted_in_compliance()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $employee = EmployeeProfile::factory()->create(['user_id' => $user->id]);

        $requirement = DocumentRequirement::factory()->create(['company_id' => $company->id]);

        // Create expired document
        $employee->documents()->create([
            'document_requirement_id' => $requirement->id,
            'status' => 'approved',
            'expiration_date' => now()->subDay(),
        ]);

        $validDocuments = $employee->documents()
            ->where('status', 'approved')
            ->where('expiration_date', '>', now())
            ->count();

        $this->assertEquals(0, $validDocuments);
    }
}
