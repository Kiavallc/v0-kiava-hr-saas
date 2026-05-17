<?php

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;

// Company-level channel - all users in the company
Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

// User-level private channel - individual user
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Employee-level channel - employee profile specific
Broadcast::channel('employee.{employeeId}', function ($user, $employeeId) {
    $employee = \App\Models\EmployeeProfile::find($employeeId);
    if (!$employee) {
        return false;
    }
    // Allow access if user is the employee or is an admin of the company
    return (int) $user->id === (int) $employee->user_id || 
           ((int) $user->company_id === (int) $employee->company_id && $user->isCompanyAdmin());
});

// Document approvals channel - admins only
Broadcast::channel('approvals.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId && 
           in_array($user->role, ['super_admin', 'company_owner', 'hr_admin', 'manager']);
});

// Audit log channel - admins only
Broadcast::channel('audit.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId && $user->isCompanyAdmin();
});
