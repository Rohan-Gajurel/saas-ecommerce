<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function clientDashboard(): JsonResponse
    {
        return $this->successResponse([
            'text' => 'Client admin dashboard',
        ], 'Client dashboard fetched successfully.');
    }

    public function superadminDashboard(): JsonResponse
    {
        return $this->successResponse([
            'text' => 'Superadmin dashboard',
        ], 'Superadmin dashboard fetched successfully.');
    }
}
