<?php

namespace App\Events;

use App\Models\EmployeeDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentExpiringSoon implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeeDocument $document;
    public int $daysUntilExpiry;

    public function __construct(EmployeeDocument $document, int $daysUntilExpiry)
    {
        $this->document = $document;
        $this->daysUntilExpiry = $daysUntilExpiry;
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
        return 'document.expiring-soon';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->document->id,
            'document_type' => $this->document->requirement->name,
            'days_until_expiry' => $this->daysUntilExpiry,
            'expiry_date' => $this->document->expiration_date?->toIso8601String(),
            'message' => "{$this->document->requirement->name} expires in {$this->daysUntilExpiry} days",
        ];
    }
}
