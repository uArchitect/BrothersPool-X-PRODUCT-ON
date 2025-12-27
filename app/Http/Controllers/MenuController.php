<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\MenuItem;
use App\Models\Category;
use App\Traits\ApiResponse;

class MenuController extends Controller
{
    use ApiResponse;
    /**
     * Display the menu management page
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $menuItems = MenuItem::with('category')
            ->select('menu_items.*', 'categories.name as category_name')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->orderBy('menu_items.name')
            ->get();

        return view('menu.index', compact('categories', 'menuItems'));
    }

    /**
     * Store a newly created menu item
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'prep_time' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'allergens' => 'nullable|string',
            'nutrition_info' => 'nullable|string'
        ]);

        $imageName = null;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('menu_images', $imageName, 'public');
        }

        $menuItem = MenuItem::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'prep_time' => $request->prep_time,
            'image' => $imageName,
            'is_available' => $request->has('is_available'),
            'allergens' => $request->allergens,
            'nutrition_info' => $request->nutrition_info,
        ]);

        return $this->createdResponse(['id' => $menuItem->id], 'Menü öğesi başarıyla oluşturuldu.');
    }

    /**
     * Update the specified menu item
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'prep_time' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'allergens' => 'nullable|string',
            'nutrition_info' => 'nullable|string'
        ]);

        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return $this->notFoundResponse('Menü öğesi bulunamadı.');
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($menuItem->image) {
                Storage::disk('public')->delete('menu_images/' . $menuItem->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('menu_images', $imageName, 'public');
        } else {
            $imageName = $menuItem->image;
        }

        $menuItem->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'prep_time' => $request->prep_time,
            'image' => $imageName,
            'is_available' => $request->has('is_available'),
            'allergens' => $request->allergens,
            'nutrition_info' => $request->nutrition_info,
        ]);

        return $this->updatedResponse(null, 'Menü öğesi başarıyla güncellendi.');
    }

    /**
     * Remove the specified menu item
     */
    public function destroy($id)
    {
        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return $this->notFoundResponse('Menü öğesi bulunamadı.');
        }

        // Delete image if exists
        if ($menuItem->image) {
            Storage::disk('public')->delete('menu_images/' . $menuItem->image);
        }

        $menuItem->delete();

        return $this->deletedResponse('Menü öğesi başarıyla silindi.');
    }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability($id)
    {
        try {
            $menuItem = MenuItem::find($id);

            if (!$menuItem) {
                return $this->notFoundResponse('Menü öğesi bulunamadı.');
            }

            $newStatus = !$menuItem->is_available;

            $menuItem->update([
                'is_available' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Menü öğesi aktif edildi.' : 'Menü öğesi pasif edildi.',
                'is_available' => $newStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('Menu toggle availability error: ' . $e->getMessage());
            return $this->serverErrorResponse('Bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get menu items for API
     */
    public function getMenuItems(Request $request)
    {
        $query = MenuItem::with('category')
            ->select('menu_items.*', 'categories.name as category_name')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id');

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('menu_items.category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->has('available_only') && $request->available_only) {
            $query->where('menu_items.is_available', true);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('menu_items.name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->orderBy('menu_items.name')->get();

        return $this->successResponse($menuItems);
    }

    /**
     * Get single menu item
     */
    public function getMenuItem($id)
    {
        $menuItem = MenuItem::with('category')
            ->select('menu_items.*', 'categories.name as category_name')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->where('menu_items.id', $id)
            ->first();

        if (!$menuItem) {
            return $this->notFoundResponse('Menü öğesi bulunamadı.');
        }

        return $this->successResponse($menuItem);
    }
}
