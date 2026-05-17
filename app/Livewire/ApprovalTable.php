<?php

namespace App\Livewire;

use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ApprovalTable extends Component
{
    public array $pendingDocuments = [];

    public function mount(): void
    {
        $this->loadPendingDocuments();
    }

    #[On('document.uploaded')]
    public function onDocumentUploaded(array $data): void
    {
        $this->loadPendingDocuments();
    }

    #[On('document.approved')]
    #[On('document.rejected')]
    public function onDocumentUpdated(): void
    {
        $this->loadPendingDocuments();
    }

    public function loadPendingDocuments(): void
    {
        $this->pendingDocuments = EmployeeDocument::where('status', 'pending')
            ->whereHas('employeeProfile', fn($q) => 
                $q->where('company_id', Auth::user()->company_id)
            )
            ->with('employeeProfile.user', 'requirement')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'employee_name' => $doc->employeeProfile->user->name,
                'document_type' => $doc->requirement->name,
                'uploaded_at' => $doc->created_at->format('M d, Y H:i'),
                'file_name' => $doc->file_path ? basename($doc->file_path) : 'N/A',
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.approval-table');
    }
}
