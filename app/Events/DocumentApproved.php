<?php

namespace App\Events;

use App\Models\EmployeeDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeeDocument $document;
    public string $approverName;

    public function __construct(EmployeeDocument $document, string $approverName)
    {
        $this->document = $document;
        $this->approverName = $approverName;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->document->employeeProfile->user_id),
            new PrivateChannel('company.' . $this->document->employeeProfile->company_id),
            new PrivateChannel('employee.' . $this->document->employeeProfile->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.approved';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->document->id,
            'document_type' => $this->document->requirement->name,
            'approver_name' => $this->approverName,
            'status' => $this->document->status,
            'message' => "Your {$this->document->requirement->name} has been approved",
        ];
    }
}
