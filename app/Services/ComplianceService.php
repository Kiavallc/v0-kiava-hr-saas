<?php

namespace App\Services;

use App\Models\EmployeeProfile;
use App\Models\DocumentRequirement;

class ComplianceService
{
    public static function calculateCompliancePercentage(EmployeeProfile $employee): float
    {
        $requirements = DocumentRequirement::where('company_id', $employee->company_id)
            ->where('is_required', true)
            ->count();

        if ($requirements === 0) {
            return 100;
        }

        $approved = $employee->documents()
            ->whereIn('status', ['approved', 'expiring_soon'])
            ->count();

        return round(($approved / $requirements) * 100, 2);
    }

    public static function getCompanyCompliancePercentage(int $companyId): float
    {
        $employees = EmployeeProfile::where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        if ($employees === 0) {
            return 100;
        }

        $totalCompliance = 0;
        foreach (EmployeeProfile::where('company_id', $companyId)->where('status', 'active')->get() as $employee) {
            $totalCompliance += self::calculateCompliancePercentage($employee);
        }

        return round($totalCompliance / $employees, 2);
    }

    public static function getMissingDocumentCount(EmployeeProfile $employee): int
    {
        return $employee->documents()
            ->whereNotIn('status', ['approved', 'expiring_soon'])
            ->orWhereNull('employee_documents.id')
            ->count();
    }

    public static function getExpiringDocumentCount(int $companyId): int
    {
        return \App\Models\EmployeeDocument::where('company_id', $companyId)
            ->where('status', 'expiring_soon')
            ->count();
    }

    public static function getExpiredDocumentCount(int $companyId): int
    {
        return \App\Models\EmployeeDocument::where('company_id', $companyId)
            ->where('status', 'expired')
            ->count();
    }

    public static function getPendingApprovalCount(int $companyId): int
    {
        return \App\Models\EmployeeDocument::where('company_id', $companyId)
            ->where('status', 'pending_review')
            ->count();
    }
}
