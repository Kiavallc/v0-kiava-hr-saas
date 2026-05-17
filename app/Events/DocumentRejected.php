<?php

namespace App\Events;

use App\Models\EmployeeDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeeDocument $document;
    public string $rejectionReason;
    public string $rejectorName;

    public function __construct(EmployeeDocument $document, string $rejectionReason, string $rejectorName)
    {
        $this->document = $document;
        $this->rejectionReason = $rejectionReason;
        $this->rejectorName = $rejectorName;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->document->employeeProfile->user_id),
            new PrivateChannel('employee.' . $this->document->employeeProfile->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.rejected';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->document->id,
            'document_type' => $this->document->requirement->name,
            'rejector_name' => $this->rejectorName,
            'rejection_reason' => $this->rejectionReason,
            'status' => $this->document->status,
            'message' => "Your {$this->document->requirement->name} was rejected: {$this->rejectionReason}",
        ];
    }
}
