<?php

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('companies.{id}', function ($user, $id) {
    return (int) $user->company_id === (int) $id;
});

Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('employees.{id}', function ($user, $id) {
    $employee = \App\Models\EmployeeProfile::find($id);
    if (!$employee) {
        return false;
    }
    return (int) $user->company_id === (int) $employee->company_id;
});

Broadcast::channel('approvals.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId && $user->isCompanyAdmin();
});

Broadcast::channel('audit.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId && $user->isCompanyAdmin();
});
