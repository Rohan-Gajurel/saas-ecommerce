@extends('layout.app')

@section('title')
    <title>All Brands - SAAS Ecommerce Backend</title>
@endsection

@section('content')
    <div class="row saas-section-anchor" id="all-brands">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="mb-3">All Brands</h4>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 border-bottom pb-3">
                        <ul class="nav nav-tabs border-0" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" href="{{ route('brands.index') }}">All Brands</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#!">Unused Brands</a>
                            </li>
                        </ul>

                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('brands.create') }}" class="text-primary fw-semibold">Add New Brand</a>
                            <a href="{{ route('brands.create') }}"
                                class="btn btn-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px; padding: 0;">
                                <i class="ti ti-plus"></i>
                            </a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3 mb-0" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif(session('delete'))
                        <div class="alert alert-danger alert-dismissible fade show mt-3 mb-0" role="alert">
                            {{ session('delete') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif(session('update'))
                        <div class="alert alert-warning alert-dismissible fade show mt-3 mb-0" role="alert">
                            {{ session('update') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('brands.index') }}" class="row g-3 mb-4">
                        <div class="col-lg-10">
                            <div class="position-relative">
                                <i class="ti ti-search position-absolute"
                                    style="left: 14px; top: 50%; transform: translateY(-50%); color: #9aa1b5;"></i>
                                <input type="text" name="search" value="{{ $search }}" class="form-control ps-5"
                                    placeholder="Search Brands ..."
                                    style="height: 48px; background-color: #f3f5fb; border: 1px solid #edf0f7;">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="dropdown">
                                <button
                                    class="btn btn-light-secondary w-100 d-flex align-items-center justify-content-center gap-1"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 48px;">
                                    Bulk Action <i class="ti ti-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button id="bulkDeleteTrigger" class="dropdown-item" type="button" disabled>
                                            Delete Selected
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>

                    <form id="bulkDeleteForm" action="{{ route('brands.bulk-delete') }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">
                                        <input id="selectAllBrands" type="checkbox" class="form-check-input">
                                    </th>
                                    <th>LOGO</th>
                                    <th>NAME</th>
                                    <th>QTY PRODUCTS</th>
                                    <th>CREATED</th>
                                    <th>CATEGORIES</th>
                                    <th class="text-end">OPTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input brand-checkbox"
                                                value="{{ $brand->id }}">
                                        </td>
                                        <td>
                                            @if ($brand->logo_url)
                                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}"
                                                    style="width: 120px; height: 80px; object-fit: contain; border-radius: 8px; background: #fff; border: 1px solid #edf0f7; padding: 4px;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                                    style="width: 120px; height: 80px;">
                                                    <strong>{{ strtoupper(substr($brand->name, 0, 1)) }}</strong>
                                                </div>
                                            @endif
                                        </td>
                                        <td><strong>{{ $brand->name }}</strong></td>
                                        <td>0</td>
                                        <td>{{ $brand->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <span>Beauty, Health & Hair, Skin...</span>
                                            <a href="#!" class="d-block text-primary">See More</a>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-light-secondary btn-sm" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('brands.edit', $brand->id) }}"
                                                            class="dropdown-item">
                                                            <i class="ti ti-edit me-1"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('brands.destroy', $brand->id) }}"
                                                            method="POST" class="delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ti ti-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No brands found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('input[name="search"]')?.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.currentTarget.closest('form').submit();
                }
            });

            document.querySelectorAll('.delete-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This brand will be deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            var selectAllBrands = document.getElementById('selectAllBrands');
            var brandCheckboxes = Array.from(document.querySelectorAll('.brand-checkbox'));
            var bulkDeleteTrigger = document.getElementById('bulkDeleteTrigger');
            var bulkDeleteForm = document.getElementById('bulkDeleteForm');

            function selectedBrandIds() {
                return brandCheckboxes
                    .filter(function(checkbox) {
                        return checkbox.checked;
                    })
                    .map(function(checkbox) {
                        return checkbox.value;
                    });
            }

            function refreshBulkDeleteState() {
                var hasSelection = selectedBrandIds().length > 0;

                if (bulkDeleteTrigger) {
                    bulkDeleteTrigger.disabled = !hasSelection;
                }

                if (!selectAllBrands) {
                    return;
                }

                var checkedCount = brandCheckboxes.filter(function(checkbox) {
                    return checkbox.checked;
                }).length;

                selectAllBrands.checked = brandCheckboxes.length > 0 && checkedCount === brandCheckboxes.length;
                selectAllBrands.indeterminate = checkedCount > 0 && checkedCount < brandCheckboxes.length;
            }

            selectAllBrands?.addEventListener('change', function(event) {
                brandCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = event.currentTarget.checked;
                });

                refreshBulkDeleteState();
            });

            brandCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', refreshBulkDeleteState);
            });

            bulkDeleteTrigger?.addEventListener('click', function() {
                var ids = selectedBrandIds();

                if (ids.length === 0 || !bulkDeleteForm) {
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Selected brands will be deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete them!'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    bulkDeleteForm.querySelectorAll('input[name="brand_ids[]"]').forEach(function(
                        input) {
                        input.remove();
                    });

                    ids.forEach(function(id) {
                        var hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'brand_ids[]';
                        hiddenInput.value = id;
                        bulkDeleteForm.appendChild(hiddenInput);
                    });

                    bulkDeleteForm.submit();
                });
            });

            refreshBulkDeleteState();
        });
    </script>
@endsection
