<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    private ProductImageService $imageService;

    public function __construct(ProductImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        Gate::authorize('viewAnyProduct', Product::class);

        $products = Product::where('business_id', auth()->user()->business_id)
            ->with(['categories', 'brand', 'primaryImage'])
            ->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        Gate::authorize('createProduct', Product::class);

        $categories = Category::where('business_id', auth()->user()->business_id)
            ->with('parent')
            ->get();
        $brands = Brand::all();

        return view('products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        Gate::authorize('createProduct', Product::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,gif,svg,webp|max:2048',
            'primary_image_index' => 'nullable|integer|min:1',
        ]);

        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);

        $data['business_id'] = auth()->user()->business_id;
        $product = Product::create($data);
        $product->categories()->sync($categoryIds);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $primaryImageIndex = $request->input('primary_image_index', 1);

            foreach ($files as $index => $file) {
                $businessId = auth()->user()->business_id;
                $storedPath = $this->imageService->storeResizedImage($file, $businessId);
                $image = $product->images()->create([
                    'path' => $storedPath,
                    'is_primary' => ($index + 1) === (int) $primaryImageIndex,
                ]);
            }

            if (!$product->images()->where('is_primary', true)->exists()) {
                $firstImage = $product->images()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => true]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('viewProduct', $product);

        $product->load(['categories', 'brand', 'images']);

        return view('products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('updateProduct', $product);

        $product->load(['images', 'categories']);

        $categories = Category::where('business_id', auth()->user()->business_id)
            ->with('parent')
            ->get();
        $brands = Brand::all();

        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('updateProduct', $product);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,gif,svg,webp|max:2048',
            'primary_image_id' => 'nullable|integer|exists:product_images,id',
            'images_to_delete' => 'nullable|string',
        ]);

        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);

        $product->update($data);
        $product->categories()->sync($categoryIds);

        if ($request->filled('primary_image_id')) {
            $primaryImage = $product->images()->where('id', $request->integer('primary_image_id'))->first();
            if ($primaryImage) {
                $product->images()->update(['is_primary' => false]);
                $primaryImage->update(['is_primary' => true]);
            }
        }

        // Delete images marked for deletion
        if ($request->filled('images_to_delete')) {
            $imagesToDelete = array_filter(explode(',', $request->input('images_to_delete')));
            if (!empty($imagesToDelete)) {
                $imagesToDeleteModels = $product->images()->whereIn('id', $imagesToDelete)->get();
                foreach ($imagesToDeleteModels as $image) {
                    $this->imageService->delete($image->path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $newPrimaryIndex = $request->input('primary_image_index');
            $newPrimaryId = null;

            foreach ($files as $index => $file) {
                $businessId = auth()->user()->business_id;
                $storedPath = $this->imageService->storeResizedImage($file, $businessId);
                $image = $product->images()->create([
                    'path' => $storedPath,
                    'is_primary' => false,
                ]);

                if (!is_null($newPrimaryIndex) && ($index + 1) === (int) $newPrimaryIndex) {
                    $newPrimaryId = $image->id;
                }
            }

            if (!is_null($newPrimaryId)) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $newPrimaryId)->update(['is_primary' => true]);
            }
        }

        if (!$product->images()->where('is_primary', true)->exists()) {
            $firstImage = $product->images()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('deleteProduct', $product);

        $product->load('images');

        foreach ($product->images as $image) {
            $this->imageService->delete($image->path);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}