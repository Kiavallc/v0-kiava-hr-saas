<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EmployeePortal extends Component
{
    public $employee;
    public $requiredDocuments;
    public $missingDocuments;
    public $expiringDocuments;
    public $expiredDocuments;

    public function mount()
    {
        $this->employee = Auth::user()->employee;
        $this->loadDocumentStatus();
    }

    public function loadDocumentStatus()
    {
        $company = Auth::user()->company;

        $this->requiredDocuments = $company->documentRequirements()
            ->where('is_required', true)
            ->get();

        $employeeDocumentIds = $this->employee->documents()
            ->where('status', 'approved')
            ->pluck('document_requirement_id');

        $this->missingDocuments = $this->requiredDocuments
            ->whereNotIn('id', $employeeDocumentIds);

        $now = now();
        $this->expiringDocuments = $this->employee->documents()
            ->where('status', 'approved')
            ->whereBetween('expiration_date', [$now, $now->addDays(30)])
            ->get();

        $this->expiredDocuments = $this->employee->documents()
            ->where('status', 'approved')
            ->where('expiration_date', '<', $now)
            ->get();
    }

    public function render()
    {
        return view('livewire.employee-portal');
    }
}
