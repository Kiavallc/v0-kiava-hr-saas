<?php

namespace App\Livewire;

use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeDocumentStatus extends Component
{
    public array $documents = [];
    public int $employeeId;

    public function mount(): void
    {
        $this->employeeId = Auth::user()->employeeProfile?->id ?? 0;
        $this->loadDocuments();
    }

    #[On('document.approved')]
    #[On('document.rejected')]
    public function onDocumentUpdated(array $data): void
    {
        $this->loadDocuments();
    }

    public function loadDocuments(): void
    {
        if (!$this->employeeId) return;

        $employee = EmployeeProfile::find($this->employeeId);
        if (!$employee) return;

        $this->documents = $employee->documents()
            ->with('requirement')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'type' => $doc->requirement->name,
                'status' => $doc->status,
                'status_label' => ucfirst($doc->status),
                'status_color' => match($doc->status) {
                    'approved' => 'green',
                    'rejected' => 'red',
                    'pending' => 'yellow',
                    'expired' => 'orange',
                    default => 'gray',
                },
                'expiry_date' => $doc->expiration_date?->format('M d, Y'),
                'created_at' => $doc->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.employee-document-status');
    }
}
