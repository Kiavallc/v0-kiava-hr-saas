<?php

namespace App\Events;

use App\Models\EmployeeDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentUploaded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeeDocument $document;
    public string $employeeName;
    public string $documentType;

    public function __construct(EmployeeDocument $document)
    {
        $this->document = $document;
        $this->employeeName = $document->employeeProfile->user->name;
        $this->documentType = $document->requirement->name;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->document->employeeProfile->company_id),
            new PrivateChannel('approvals.' . $this->document->employeeProfile->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.uploaded';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->document->id,
            'employee_name' => $this->employeeName,
            'document_type' => $this->documentType,
            'status' => $this->document->status,
            'created_at' => $this->document->created_at->toIso8601String(),
            'message' => "{$this->employeeName} uploaded {$this->documentType}",
        ];
    }
}
