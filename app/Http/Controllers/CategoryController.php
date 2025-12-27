<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display categories index page
     */
    public function index()
    {
        $categories = DB::table('categories')
            ->orderBy('level', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        // Check for duplicate name in same level
        $duplicateQuery = DB::table('categories')->where('name', $request->name);
        if ($request->parent_id) {
            $duplicateQuery->where('parent_id', $request->parent_id);
        } else {
            $duplicateQuery->whereNull('parent_id');
        }
        
        if ($duplicateQuery->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu seviyede aynı isimde kategori zaten mevcut.'
            ], 400);
        }

        $data = $request->except(['_token', 'image']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Calculate level and path
        if ($request->parent_id) {
            $parent = DB::table('categories')->find($request->parent_id);
            $data['level'] = $parent->level + 1;
            $data['path'] = $parent->path ? $parent->path . '/' . $parent->id : $parent->id;
        } else {
            $data['level'] = 0;
            $data['path'] = null;
        }

        // Auto-calculate sort_order (add to end of same level)
        $maxSortOrder = DB::table('categories')
            ->where('parent_id', $request->parent_id)
            ->max('sort_order');
        $data['sort_order'] = ($maxSortOrder ?? -1) + 1;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('category_images', $imageName, 'public');
            $data['image'] = $imageName;
        }

        $categoryId = DB::table('categories')->insertGetId($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla oluşturuldu.',
            'data' => ['id' => $categoryId]
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $category = DB::table('categories')->where('id', $id)->first();
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı.'
            ], 404);
        }

        // Prevent moving category to its own child
        if ($request->parent_id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kendi alt kategorisi olamaz.'
            ], 400);
        }

        // Check if moving to a child category (prevent circular reference)
        if ($request->parent_id) {
            $parentPath = DB::table('categories')->where('id', $request->parent_id)->value('path');
            if ($parentPath && strpos($parentPath, $id) !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori kendi alt kategorisinin altına taşınamaz.'
                ], 400);
            }
        }

        // Check for duplicate name in same level
        $duplicateQuery = DB::table('categories')->where('name', $request->name)->where('id', '!=', $id);
        if ($request->parent_id) {
            $duplicateQuery->where('parent_id', $request->parent_id);
        } else {
            $duplicateQuery->whereNull('parent_id');
        }
        
        if ($duplicateQuery->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu seviyede aynı isimde kategori zaten mevcut.'
            ], 400);
        }

        $data = $request->except(['_token', '_method', 'image']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['updated_at'] = now();

        // Calculate new level and path
        if ($request->parent_id) {
            $parent = DB::table('categories')->find($request->parent_id);
            $data['level'] = $parent->level + 1;
            $data['path'] = $parent->path ? $parent->path . '/' . $parent->id : $parent->id;
        } else {
            $data['level'] = 0;
            $data['path'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete('category_images/' . $category->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('category_images', $imageName, 'public');
            $data['image'] = $imageName;
        }

        DB::table('categories')->where('id', $id)->update($data);

        // Update children's level and path if parent changed
        if ($category->parent_id != $request->parent_id) {
            $this->updateChildrenPaths($id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla güncellendi.'
        ]);
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı.'
            ], 404);
        }

        // Check if category has menu items
        $menuItemsCount = DB::table('menu_items')->where('category_id', $id)->count();
        if ($menuItemsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kategoriye ait menü öğeleri bulunduğu için silinemez.'
            ], 400);
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete('category_images/' . $category->image);
        }

        DB::table('categories')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla silindi.'
        ]);
    }


    /**
     * Get categories for API (hierarchical tree structure)
     */
    public function getCategories(Request $request)
    {
        $query = DB::table('categories');

        // Filter by active status
        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', 1);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('level', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Build hierarchical tree
        $tree = $this->buildCategoryTree($categories);

        return response()->json([
            'success' => true,
            'data' => $tree
        ]);
    }

    /**
     * Get categories for dropdown (flat list with indentation)
     */
    public function getCategoriesForDropdown(Request $request)
    {
        $query = DB::table('categories');

        // Filter by active status
        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', 1);
        }

        $categories = $query->orderBy('level', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Add indentation for display
        $categories->transform(function ($category) {
            $category->display_name = str_repeat('— ', $category->level) . $category->name;
            return $category;
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get single category
     */
    public function getCategory($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update category sort order
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        try {
            foreach ($request->categories as $category) {
                DB::table('categories')
                    ->where('id', $category['id'])
                    ->update([
                        'sort_order' => $category['sort_order'],
                        'updated_at' => now()
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sıralama başarıyla güncellendi.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Category sort order update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenirken hata oluştu.'
            ], 500);
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id)
    {
        try {
            $category = DB::table('categories')->where('id', $id)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı.'
                ], 404);
            }

            $newStatus = $category->is_active ? 0 : 1;
            
            $updated = DB::table('categories')
                ->where('id', $id)
                ->update([
                    'is_active' => $newStatus,
                    'updated_at' => now()
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => $newStatus ? 'Kategori aktif edildi.' : 'Kategori pasif edildi.',
                    'is_active' => $newStatus
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori durumu güncellenemedi.'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Category toggle status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build hierarchical category tree
     */
    private function buildCategoryTree($categories)
    {
        $tree = [];
        $lookup = [];

        // First pass: create lookup array
        foreach ($categories as $category) {
            $category->children = [];
            $lookup[$category->id] = $category;
        }

        // Second pass: build tree
        foreach ($categories as $category) {
            if ($category->parent_id === null) {
                $tree[] = $category;
            } else {
                if (isset($lookup[$category->parent_id])) {
                    $lookup[$category->parent_id]->children[] = $category;
                }
            }
        }

        return $tree;
    }

    /**
     * Update children paths recursively
     */
    private function updateChildrenPaths($parentId)
    {
        $children = DB::table('categories')->where('parent_id', $parentId)->get();
        
        foreach ($children as $child) {
            $parent = DB::table('categories')->find($parentId);
            $newLevel = $parent->level + 1;
            $newPath = $parent->path ? $parent->path . '/' . $parentId : $parentId;
            
            DB::table('categories')->where('id', $child->id)->update([
                'level' => $newLevel,
                'path' => $newPath,
                'updated_at' => now()
            ]);
            
            // Recursively update children
            $this->updateChildrenPaths($child->id);
        }
    }
}
