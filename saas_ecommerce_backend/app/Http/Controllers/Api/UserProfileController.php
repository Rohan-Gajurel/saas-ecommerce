<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return $this->successResponse($request->user(), 'User profile fetched successfully.');
    }

    public function update(UpdateUserProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $request->user()->update([
            'name' => $validated['full_name'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? null,
            'phone_no' => $validated['phone_no'] ?? null,
        ]);

        return $this->successResponse($request->user()->fresh(), 'User profile updated successfully.');
    }
}
