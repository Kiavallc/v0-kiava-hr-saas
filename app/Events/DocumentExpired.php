<?php

namespace App\Events;

use App\Models\EmployeeDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeeDocument $document;

    public function __construct(EmployeeDocument $document)
    {
        $this->document = $document;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->document->employeeProfile->user_id),
            new PrivateChannel('company.' . $this->document->employeeProfile->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.expired';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->document->id,
            'document_type' => $this->document->requirement->name,
            'expired_at' => $this->document->expiration_date?->toIso8601String(),
            'status' => 'expired',
            'message' => "{$this->document->requirement->name} has expired",
        ];
    }
}
