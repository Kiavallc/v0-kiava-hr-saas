<?php

namespace App\Http\Controllers;

use App\Events\DocumentApproved;
use App\Events\DocumentExpired;
use App\Events\DocumentExpiringSoon;
use App\Events\DocumentRejected;
use App\Events\DocumentUploaded;
use App\Events\DashboardStatsUpdated;
use App\Models\Company;
use App\Models\EmployeeDocument;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Auth;

class RealtimeTestController extends Controller
{
    public function index()
    {
        return view('realtime-test');
    }

    public function simulateDocumentUploaded()
    {
        $employee = EmployeeProfile::whereHas('company', fn($q) => 
            $q->where('id', Auth::user()->company_id)
        )->first();

        if (!$employee) {
            return response()->json(['error' => 'No employee found'], 404);
        }

        $document = EmployeeDocument::factory()->create([
            'employee_profile_id' => $employee->id,
        ]);

        event(new DocumentUploaded($document));

        return response()->json([
            'success' => true,
            'message' => 'DocumentUploaded event broadcasted',
            'event' => 'document.uploaded',
            'data' => [
                'document_id' => $document->id,
                'employee' => $employee->user->name,
            ],
        ]);
    }

    public function simulateDocumentApproved()
    {
        $document = EmployeeDocument::where('status', 'pending')
            ->whereHas('employeeProfile.company', fn($q) => 
                $q->where('id', Auth::user()->company_id)
            )
            ->first();

        if (!$document) {
            return response()->json(['error' => 'No pending document found'], 404);
        }

        $document->update(['status' => 'approved']);
        event(new DocumentApproved($document, Auth::user()->name));

        return response()->json([
            'success' => true,
            'message' => 'DocumentApproved event broadcasted',
            'event' => 'document.approved',
            'data' => [
                'document_id' => $document->id,
                'status' => 'approved',
            ],
        ]);
    }

    public function simulateDocumentRejected()
    {
        $document = EmployeeDocument::where('status', 'pending')
            ->whereHas('employeeProfile.company', fn($q) => 
                $q->where('id', Auth::user()->company_id)
            )
            ->first();

        if (!$document) {
            return response()->json(['error' => 'No pending document found'], 404);
        }

        $document->update(['status' => 'rejected', 'rejection_reason' => 'Test rejection']);
        event(new DocumentRejected($document, 'Test rejection', Auth::user()->name));

        return response()->json([
            'success' => true,
            'message' => 'DocumentRejected event broadcasted',
            'event' => 'document.rejected',
            'data' => [
                'document_id' => $document->id,
                'status' => 'rejected',
            ],
        ]);
    }

    public function simulateDocumentExpiringSoon()
    {
        $document = EmployeeDocument::where('status', 'approved')
            ->whereHas('employeeProfile.company', fn($q) => 
                $q->where('id', Auth::user()->company_id)
            )
            ->first();

        if (!$document) {
            return response()->json(['error' => 'No approved document found'], 404);
        }

        event(new DocumentExpiringSoon($document, 7));

        return response()->json([
            'success' => true,
            'message' => 'DocumentExpiringSoon event broadcasted',
            'event' => 'document.expiring-soon',
            'data' => [
                'document_id' => $document->id,
                'days_until_expiry' => 7,
            ],
        ]);
    }

    public function simulateDocumentExpired()
    {
        $document = EmployeeDocument::where('status', 'approved')
            ->whereHas('employeeProfile.company', fn($q) => 
                $q->where('id', Auth::user()->company_id)
            )
            ->first();

        if (!$document) {
            return response()->json(['error' => 'No approved document found'], 404);
        }

        $document->update(['status' => 'expired']);
        event(new DocumentExpired($document));

        return response()->json([
            'success' => true,
            'message' => 'DocumentExpired event broadcasted',
            'event' => 'document.expired',
            'data' => [
                'document_id' => $document->id,
                'status' => 'expired',
            ],
        ]);
    }

    public function simulateDashboardStats()
    {
        $company = Company::find(Auth::user()->company_id);
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        $stats = [
            'total_employees' => $company->employeeProfiles()->count(),
            'documents_uploaded' => EmployeeDocument::whereHas('employeeProfile', fn($q) => 
                $q->where('company_id', $company->id)
            )->count(),
            'documents_approved' => EmployeeDocument::whereHas('employeeProfile', fn($q) => 
                $q->where('company_id', $company->id)
            )->where('status', 'approved')->count(),
            'documents_expiring' => EmployeeDocument::whereHas('employeeProfile', fn($q) => 
                $q->where('company_id', $company->id)
            )
                ->where('status', 'approved')
                ->where('expiration_date', '<=', now()->addDays(30))
                ->where('expiration_date', '>', now())
                ->count(),
        ];

        event(new DashboardStatsUpdated($company->id, $stats));

        return response()->json([
            'success' => true,
            'message' => 'DashboardStatsUpdated event broadcasted',
            'event' => 'dashboard.updated',
            'data' => $stats,
        ]);
    }
}
