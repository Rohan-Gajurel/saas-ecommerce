<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessRequest;
use App\Http\Requests\Api\UpdateBusinessRequest;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $businesses = $request->user()->businesses()->latest()->get();

        return $this->successResponse($businesses, 'Businesses fetched successfully.');
    }

    public function store(StoreBusinessRequest $request): JsonResponse
    {
        $business = $request->user()->businesses()->create($request->validated());

        return $this->successResponse($business, 'Business created successfully.', 201);
    }

    public function show(Request $request, Business $business): JsonResponse
    {
        if ($business->user_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to access this business.', 403);
        }

        return $this->successResponse($business, 'Business fetched successfully.');
    }

    public function update(UpdateBusinessRequest $request, Business $business): JsonResponse
    {
        if ($business->user_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to update this business.', 403);
        }

        $business->update($request->validated());

        return $this->successResponse($business->fresh(), 'Business updated successfully.');
    }

    public function destroy(Request $request, Business $business): JsonResponse
    {
        if ($business->user_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to delete this business.', 403);
        }

        $business->delete();

        return $this->successResponse(null, 'Business deleted successfully.');
    }
}
