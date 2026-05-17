<?php

namespace App\Events;

use App\Models\AuditLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditLogCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AuditLog $auditLog;

    public function __construct(AuditLog $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->auditLog->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'audit.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->auditLog->id,
            'user_name' => $this->auditLog->user->name,
            'action' => $this->auditLog->action,
            'model' => $this->auditLog->auditable_type,
            'ip_address' => $this->auditLog->ip_address,
            'created_at' => $this->auditLog->created_at->toIso8601String(),
        ];
    }
}
