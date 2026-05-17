<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        $data = [
            'employeeCount' => 0,
            'compliancePercentage' => 0,
            'expiringCount' => 0,
            'pendingCount' => 0,
        ];

        return view('dashboard', $data);
    }
}
