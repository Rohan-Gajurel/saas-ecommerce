@extends('layout.app')

@section('title')
    <title>Superadmin Dashboard - SAAS Ecommerce Backend</title>
@endsection

@section('content')
    <div class="mb-5">
        <h2 class="mb-2" style="font-weight: 700; font-size: 2rem; color: #1a202c;">Superadmin • Tenants</h2>
        <p class="text-muted" style="font-size: 1rem;">Multi-tenant management and operations</p>
    </div>

    <!-- Success Message -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tenants Section -->
    <div class="row saas-section-anchor" id="tenants">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">Tenants Management</h5>
                            <small class="text-muted">Create, update, and manage all tenants</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createTenantModal">
                                <i class="ti ti-plus"></i> Create Tenant
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tenant</th>
                                    <th>Owner</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tenants as $tenant)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $tenant->business_name }}</strong>
                                                <small class="text-muted">
                                                    {{ $tenant->desired_url_name ?? 'N/A' }} •
                                                    {{ $tenant->business_email ?? 'No email' }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $tenant->user?->full_name ?? ($tenant->user?->name ?? 'N/A') }}</strong>
                                                <small class="text-muted">{{ $tenant->user?->email ?? 'No email' }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($tenant->isActive())
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Suspended</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-light-secondary" data-bs-toggle="modal"
                                                    data-bs-target="#editTenantModal"
                                                    data-tenant="{{ json_encode($tenant) }}">
                                                    Edit
                                                </button>
                                                @if ($tenant->isActive())
                                                    <form action="{{ route('tenants.suspend', $tenant->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light-warning"
                                                            onclick="return confirm('Suspend this tenant? All operations will be halted.')">
                                                            Suspend
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('tenants.reactivate', $tenant->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light-success">
                                                            Reactivate
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('tenants.destroy', $tenant->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light-danger"
                                                        onclick="return confirm('Delete this tenant? This action cannot be undone.')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <p class="mb-0">No tenants found. <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#createTenantModal">Create one now</a></p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($tenants->hasPages())
                        <div class="d-flex justify-content-between align-items-center p-3 border-top">
                            <div class="text-muted small">
                                Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }}
                                of {{ $tenants->total() }} tenants
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    {{ $tenants->links() }}
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Tenant Modal -->
    <div class="modal fade" id="createTenantModal" tabindex="-1" aria-labelledby="createTenantLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTenantLabel">Create New Tenant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tenants.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="owner_name" class="form-label">Owner Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('owner_full_name') is-invalid @enderror"
                                    id="owner_name" name="owner_full_name" placeholder="e.g., John Doe"
                                    value="{{ old('owner_full_name') }}" required>
                                @error('owner_full_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="owner_email" class="form-label">Owner Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('owner_email') is-invalid @enderror"
                                    id="owner_email" name="owner_email" placeholder="e.g., owner@acme.com"
                                    value="{{ old('owner_email') }}" required>
                                @error('owner_email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_name" class="form-label">Business Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror"
                                    id="business_name" name="business_name" placeholder="e.g., ACME Store"
                                    value="{{ old('business_name') }}" required>
                                @error('business_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="desired_url_name" class="form-label">URL Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('desired_url_name') is-invalid @enderror"
                                    id="desired_url_name" name="desired_url_name" placeholder="e.g., acme-store"
                                    value="{{ old('desired_url_name') }}" required>
                                @error('desired_url_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_location" class="form-label">Location <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('business_location') is-invalid @enderror"
                                    id="business_location" name="business_location" placeholder="e.g., New York, USA"
                                    value="{{ old('business_location') }}" required>
                                @error('business_location')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_registration_number" class="form-label">Registration Number <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('business_registration_number') is-invalid @enderror"
                                    id="business_registration_number" name="business_registration_number"
                                    placeholder="e.g., REG123456" value="{{ old('business_registration_number') }}"
                                    required>
                                @error('business_registration_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_phone" class="form-label">Phone <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('business_phone') is-invalid @enderror"
                                    id="business_phone" name="business_phone" placeholder="e.g., +1234567890"
                                    value="{{ old('business_phone') }}" required>
                                @error('business_phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('business_email') is-invalid @enderror"
                                    id="business_email" name="business_email" placeholder="e.g., info@acme.com"
                                    value="{{ old('business_email') }}">
                                @error('business_email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Tenant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Tenant Modal -->
    <div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTenantLabel">Edit Tenant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTenantForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_owner_name" class="form-label">Owner Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_owner_name" readonly>
                                <small class="text-muted">Owner is the original creator of this business and cannot be
                                    changed here.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_business_name" class="form-label">Business Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror"
                                    id="edit_business_name" name="business_name" required>
                                @error('business_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_desired_url_name" class="form-label">URL Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('desired_url_name') is-invalid @enderror"
                                    id="edit_desired_url_name" name="desired_url_name" required>
                                @error('desired_url_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_business_location" class="form-label">Location <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('business_location') is-invalid @enderror"
                                    id="edit_business_location" name="business_location" required>
                                @error('business_location')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_business_registration_number" class="form-label">Registration Number
                                    <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('business_registration_number') is-invalid @enderror"
                                    id="edit_business_registration_number" name="business_registration_number" required>
                                @error('business_registration_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_business_phone" class="form-label">Phone <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('business_phone') is-invalid @enderror"
                                    id="edit_business_phone" name="business_phone" required>
                                @error('business_phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_business_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('business_email') is-invalid @enderror"
                                    id="edit_business_email" name="business_email">
                                @error('business_email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Tenant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle edit modal population
                const editButtons = document.querySelectorAll('[data-bs-target="#editTenantModal"]');
                const editModal = document.getElementById('editTenantModal');
                const editForm = document.getElementById('editTenantForm');

                editButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const tenant = JSON.parse(this.dataset.tenant);

                        // Populate form fields
                        document.getElementById('edit_owner_name').value = tenant.user?.full_name ||
                            tenant.user
                            ?.name || 'N/A';
                        document.getElementById('edit_business_name').value = tenant.business_name;
                        document.getElementById('edit_desired_url_name').value = tenant
                            .desired_url_name;
                        document.getElementById('edit_business_location').value = tenant
                            .business_location;
                        document.getElementById('edit_business_registration_number').value = tenant
                            .business_registration_number;
                        document.getElementById('edit_business_phone').value = tenant.business_phone;
                        document.getElementById('edit_business_email').value = tenant.business_email ||
                            '';

                        // Update form action
                        editForm.action = `/tenants/${tenant.id}`;
                    });
                });
            });
        </script>
    @endpush
@endsection
