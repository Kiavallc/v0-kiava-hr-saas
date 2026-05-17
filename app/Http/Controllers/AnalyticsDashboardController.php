<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->company;
        $analyticsService = new AnalyticsService();

        $stats = $analyticsService->getDashboardStats($company);
        $complianceReport = $analyticsService->generateComplianceReport($company);
        $forecast = $analyticsService->getExpirationForecast($company);
        $activityTrend = $analyticsService->getUserActivityTrend($company);

        return view('analytics.dashboard', [
            'stats' => $stats,
            'complianceReport' => $complianceReport,
            'forecast' => $forecast,
            'activityTrend' => $activityTrend,
        ]);
    }

    public function complianceReport()
    {
        $company = auth()->user()->company;
        $analyticsService = new AnalyticsService();

        $report = $analyticsService->generateComplianceReport($company);
        $documentTypeStats = $analyticsService->getDocumentTypeStats($company);

        return view('analytics.compliance-report', [
            'report' => $report,
            'documentTypeStats' => $documentTypeStats,
        ]);
    }

    public function exportReport()
    {
        $company = auth()->user()->company;
        $analyticsService = new AnalyticsService();
        $report = $analyticsService->generateComplianceReport($company);

        return response()->json($report->toArray());
    }
}
