<?php

namespace App\Http\Controllers\v1;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController
{
  protected $categoryService;

  public function __construct(CategoryService $categoryService)
  {
    $this->categoryService = $categoryService;
  }

  public function getCategoriesPaginated(Request $request)
  {
    return response()->json($this->categoryService->getCategoriesPaginated($request->all(), true));
  }

  public function getCategoryById($id)
  {
    return response()->json($this->categoryService->findById($id));
  }

  public function createCategory(Request $request)
  {
    $request->validate([
      'name' => 'required|string',
      'parent_category_id' => 'nullable|exists:categories,id',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    return response()->json($this->categoryService->create($request->all()));
  }

  public function updateCategory(Request $request, $id)
  {
    $request->validate([
      'name' => 'required|string',
      'parent_category_id' => 'nullable|exists:categories,id',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    return response()->json($this->categoryService->update($id, $request->all()));
  }

  public function deleteCategory($id)
  {
    return response()->json($this->categoryService->delete($id));
  }

  public function getSubCategoriesPaginated(Request $request)
  {
    return response()->json($this->categoryService->getCategoriesPaginated($request->all(), false));
  }

  public function getSubCategoryById($id)
  {
    return response()->json($this->categoryService->findById($id));
  }

  public function createSubCategory(Request $request)
  {
    $request->validate([
      'name' => 'required|string',
      'parent_category_id' => 'required|exists:categories,id',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    return response()->json($this->categoryService->create($request->all(), false));
  }

  public function updateSubCategory(Request $request, $id)
  {
    $request->validate([
      'name' => 'required|string',
      'parent_category_id' => 'required|exists:categories,id',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    return response()->json($this->categoryService->update($id, $request->all(), false));
  }

  public function deleteSubCategory($id)
  {
    return response()->json($this->categoryService->delete($id));
  }
}
