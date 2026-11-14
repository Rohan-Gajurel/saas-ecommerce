<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Display tenant dashboard
     */
    public function dashboard(): View
    {
        $tenants = Business::with('user')->latest()->paginate(10);

        return view('dashboard.superadmin', compact('tenants'));
    }

    /**
     * Store new tenant
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'owner_full_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email',

            'business_name' => 'required|string|max:255|unique:businesses,business_name',
            'desired_url_name' => 'required|string|max:255|unique:businesses,desired_url_name',
            'business_location' => 'required|string|max:255',
            'business_registration_number' => 'required|string|unique:businesses,business_registration_number',

            'business_email' => 'nullable|email',
            'business_phone' => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {

            // Generate password (you can email this later)
            $password = Str::random(8);

            // Create Owner
            $owner = User::create([
                'name' => $validated['owner_full_name'],
                'full_name' => $validated['owner_full_name'],
                'email' => $validated['owner_email'],
                'phone_no' => $validated['business_phone'],
                'password' => Hash::make($password),
            ]);

            // Create Business (Tenant)
            Business::create([
                'user_id' => $owner->id,
                'business_name' => $validated['business_name'],
                'desired_url_name' => $validated['desired_url_name'],
                'business_location' => $validated['business_location'],
                'business_registration_number' => $validated['business_registration_number'],
                'business_email' => $validated['business_email'] ?? null,
                'business_phone' => $validated['business_phone'],
                'status' => 'active', // important
            ]);

            // TODO: Send email with $password (later)
        });

        return redirect()->route('tenants.dashboard')
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Update tenant
     */
    public function update(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255|unique:businesses,business_name,' . $business->id,
            'desired_url_name' => 'required|string|max:255|unique:businesses,desired_url_name,' . $business->id,
            'business_location' => 'required|string|max:255',
            'business_registration_number' => 'required|string|unique:businesses,business_registration_number,' . $business->id,
            'business_email' => 'nullable|email',
            'business_phone' => 'required|string|max:20',
        ]);

        $business->update($validated);

        return redirect()->route('tenants.dashboard')
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Delete tenant (with owner)
     */
    public function destroy(Business $business): RedirectResponse
    {
        DB::transaction(function () use ($business) {
            $business->user()->delete(); // delete owner
            $business->delete();
        });

        return redirect()->route('tenants.dashboard')
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * Suspend tenant
     */
    public function suspend(Business $business): RedirectResponse
    {
        $business->update(['status' => 'suspended']);

        return redirect()->route('tenants.dashboard')
            ->with('success', 'Tenant suspended successfully.');
    }

    /**
     * Reactivate tenant
     */
    public function reactivate(Business $business): RedirectResponse
    {
        $business->update(['status' => 'active']);

        return redirect()->route('tenants.dashboard')
            ->with('success', 'Tenant reactivated successfully.');
    }
}