<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\ComplianceReport;
use App\Models\Company;
use Carbon\Carbon;

class AnalyticsService
{
    public function recordEvent($company, $eventName, $eventCategory, $data = [])
    {
        AnalyticsEvent::create([
            'company_id' => $company->id ?? $company,
            'event_name' => $eventName,
            'event_category' => $eventCategory,
            'event_data' => $data,
        ]);
    }

    public function getDashboardStats(Company $company, $days = 30)
    {
        $startDate = now()->subDays($days);

        $events = AnalyticsEvent::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'total_events' => $events->count(),
            'document_uploads' => $events->where('event_category', 'document_upload')->count(),
            'document_approvals' => $events->where('event_category', 'document_approval')->count(),
            'document_expirations' => $events->where('event_category', 'document_expiry')->count(),
            'user_logins' => $events->where('event_category', 'user_login')->count(),
            'subscription_changes' => $events->where('event_category', 'subscription_change')->count(),
        ];
    }

    public function generateComplianceReport(Company $company, $date = null)
    {
        $date = $date ?? now()->toDateString();

        $employees = $company->employees()->count();
        $submitted = $company->employees()
            ->whereHas('documents', fn($q) => $q->where('status', 'approved'))
            ->count();

        $approved = \DB::table('employee_documents')
            ->where('company_id', $company->id)
            ->where('status', 'approved')
            ->count();

        $pending = \DB::table('employee_documents')
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->count();

        $expired = \DB::table('employee_documents')
            ->where('company_id', $company->id)
            ->where('status', 'expired')
            ->count();

        $expiring = \DB::table('employee_documents')
            ->where('company_id', $company->id)
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>', now())
            ->count();

        $compliance = $employees > 0 ? round(($submitted / $employees) * 100, 2) : 0;

        $report = ComplianceReport::updateOrCreate(
            ['company_id' => $company->id, 'report_date' => $date],
            [
                'total_employees' => $employees,
                'documents_submitted' => $submitted,
                'documents_approved' => $approved,
                'documents_pending' => $pending,
                'documents_expired' => $expired,
                'documents_expiring_soon' => $expiring,
                'compliance_percentage' => $compliance,
                'breakdown_by_document_type' => $this->getBreakdownByType($company),
            ]
        );

        return $report;
    }

    public function getExpirationForecast(Company $company, $weeks = 12)
    {
        $forecast = [];

        for ($i = 0; $i < $weeks; $i++) {
            $startDate = now()->addWeeks($i);
            $endDate = $startDate->copy()->addWeek();

            $count = \DB::table('employee_documents')
                ->where('company_id', $company->id)
                ->whereDate('expiration_date', '>=', $startDate)
                ->whereDate('expiration_date', '<', $endDate)
                ->count();

            $forecast[] = [
                'week' => $startDate->format('Y-m-d'),
                'expiring_count' => $count,
            ];
        }

        return $forecast;
    }

    public function getUserActivityTrend(Company $company, $days = 30)
    {
        $events = AnalyticsEvent::where('company_id', $company->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->get()
            ->groupBy(fn($event) => $event->created_at->toDateString());

        $trend = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $trend[] = [
                'date' => $date,
                'count' => $events->get($date, collect())->count(),
            ];
        }

        return $trend;
    }

    public function getDocumentTypeStats(Company $company)
    {
        return \DB::table('document_requirements')
            ->where('company_id', $company->id)
            ->select('name', \DB::raw('COUNT(*) as total'))
            ->groupBy('name')
            ->get();
    }

    private function getBreakdownByType(Company $company)
    {
        return $this->getDocumentTypeStats($company)
            ->mapWithKeys(fn($type) => [$type->name => $type->total])
            ->toArray();
    }
}
