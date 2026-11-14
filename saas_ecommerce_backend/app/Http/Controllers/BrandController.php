<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandLogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private readonly BrandLogoService $brandLogoService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $businessId = $this->currentBusinessId();

        $brands = Brand::query()
            ->where('business_id', $businessId)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('brand.index', compact('brands', 'search'));
    }

    public function create(): View
    {
        return view('brand.create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $businessId = $this->currentBusinessId();
        $data['business_id'] = $businessId;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->brandLogoService->storeResizedLogo($request->file('logo'), $businessId);
        }


        Brand::create($data);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        if ($brand->business_id !== $this->currentBusinessId()) {
            abort(403);
        }

        return view('brand.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        if ($brand->business_id !== $this->currentBusinessId()) {
            abort(403);
        }

        $data = $request->validated();
        $businessId = $this->currentBusinessId();
        $data['business_id'] = $businessId;

        if ($request->hasFile('logo')) {
            $this->brandLogoService->delete($brand->logo_path);
            $data['logo_path'] = $this->brandLogoService->storeResizedLogo($request->file('logo'), $businessId);
        }

        $brand->update($data);

        return redirect()->route('brands.index')->with('update', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->business_id !== $this->currentBusinessId()) {
            abort(403);
        }

        $this->brandLogoService->delete($brand->logo_path);

        $brand->delete();

        return redirect()->route('brands.index')->with('delete', 'Brand deleted successfully.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_ids' => ['required', 'array', 'min:1'],
            'brand_ids.*' => ['integer'],
        ]);

        $businessId = $this->currentBusinessId();

        $brands = Brand::query()
            ->where('business_id', $businessId)
            ->whereIn('id', $validated['brand_ids'])
            ->get();

        if ($brands->isEmpty()) {
            return redirect()->route('brands.index')->with('delete', 'No valid brands selected.');
        }

        foreach ($brands as $brand) {
            $this->brandLogoService->delete($brand->logo_path);
            $brand->delete();
        }

        return redirect()->route('brands.index')->with('delete', $brands->count() . ' brand(s) deleted successfully.');
    }

    private function currentBusinessId(): ?int
    {
        return auth()->user()?->business?->id;
    }
}
