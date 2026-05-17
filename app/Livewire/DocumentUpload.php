<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentUpload extends Component
{
    use WithFileUploads;

    public $documentFile;
    public $expirationDate;
    public $documentRequirementId;
    public $notes;
    public $isUploading = false;

    public function upload()
    {
        $this->validate([
            'documentFile' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'expirationDate' => 'required|date|after:today',
            'documentRequirementId' => 'required|exists:document_requirements,id',
        ]);

        $this->isUploading = true;

        try {
            $path = $this->documentFile->store('employee-documents', 'private');

            $employee = Auth::user()->employee;

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_requirement_id' => $this->documentRequirementId,
                'file_path' => $path,
                'expiration_date' => $this->expirationDate,
                'status' => 'pending',
                'notes' => $this->notes,
            ]);

            $this->dispatch('documentUploaded');
            $this->reset(['documentFile', 'expirationDate', 'notes']);
        } finally {
            $this->isUploading = false;
        }
    }

    public function render()
    {
        return view('livewire.document-upload');
    }
}
