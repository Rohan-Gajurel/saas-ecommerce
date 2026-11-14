<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\ImageManager;

class CategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        $categories = Category::where('business_id', auth()->user()->business_id)->get();

        return view('category.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('create', Category::class);
        $categories = Category::all();
        return view('category.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Category::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,gif,svg,webp|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            if ($image->getClientOriginalExtension() === 'svg') {
                // Directly save SVG files without resizing
                $image->move(storage_path('app/public/categories/'), $filename);
            } else {
                $manager = new ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                $img = $manager->read($image->getPathname())
                    ->resize(32, 32);

                $img->save(storage_path('app/public/categories/' . $filename));
            }

            $data['image'] = 'categories/' . $filename;
        }

        $data['business_id'] = auth()->user()->business_id;
        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        Gate::authorize('update', $category); 

        $categories = Category::where('id', '!=', $id)->get();
        return view('category.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        Gate::authorize('update', $category);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,gif,svg,webp|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {

            if ($category->image) {
                $oldPath = storage_path('app/public/' . $category->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            if ($image->getClientOriginalExtension() === 'svg') {
                // Directly save SVG files without resizing
                $image->move(storage_path('app/public/categories/'), $filename);
            } else {
                $manager = new ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                $img = $manager->read($image->getPathname())
                    ->resize(32, 32);

                $img->save(storage_path('app/public/categories/' . $filename));
            }

            $data['image'] = 'categories/' . $filename;
        }

        $data['business_id'] = auth()->user()->business_id;
        $category->update($data);

        return redirect()->route('categories.index')->with('update', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        
        $category = Category::findOrFail($id);
        Gate::authorize('delete', $category);

        if ($category->image) {
            $imagePath = storage_path('app/public/' . $category->image);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $category->delete();

        return redirect()->route('categories.index')->with('delete', 'Category deleted successfully.');
    }
}