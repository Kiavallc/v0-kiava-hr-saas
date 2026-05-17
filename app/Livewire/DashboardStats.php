<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardStats extends Component
{
    public int $companyId;
    public int $totalEmployees = 0;
    public int $documentsUploaded = 0;
    public int $documentsApproved = 0;
    public int $documentsExpiring = 0;
    public int $compliancePercentage = 0;

    public function mount(): void
    {
        $this->companyId = Auth::user()->company_id;
        $this->loadStats();
    }

    #[On('dashboard.updated')]
    public function onDashboardUpdated(array $data): void
    {
        if ($data['company_id'] == $this->companyId) {
            $this->loadStats();
        }
    }

    #[On('document.uploaded')]
    #[On('document.approved')]
    #[On('document.rejected')]
    public function onDocumentUpdated(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $company = Company::find($this->companyId);
        if (!$company) return;

        $this->totalEmployees = $company->employeeProfiles()->count();
        
        $documents = EmployeeDocument::whereHas('employeeProfile', fn($q) => 
            $q->where('company_id', $this->companyId)
        );

        $this->documentsUploaded = $documents->count();
        $this->documentsApproved = (clone $documents)->where('status', 'approved')->count();
        $this->documentsExpiring = (clone $documents)
            ->where('status', 'approved')
            ->where('expiration_date', '<=', now()->addDays(30))
            ->where('expiration_date', '>', now())
            ->count();

        $this->compliancePercentage = $this->totalEmployees > 0
            ? round(($this->documentsApproved / ($this->totalEmployees * 10)) * 100)
            : 0;
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
