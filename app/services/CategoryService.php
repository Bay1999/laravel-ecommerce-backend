<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CategoryService
{
  protected $categoryRepository;

  public function __construct(CategoryRepository $categoryRepository)
  {
    $this->categoryRepository = $categoryRepository;
  }

  public function getAll()
  {
    return $this->categoryRepository->all();
  }

  public function getCategoriesPaginated($data, $isParent = true)
  {
    $page = $data['page'] ?? 1;
    $perPage = $data['limit'] ?? 10;
    $sortBy = $data['sort_by'] ?? 'id';
    $sortOrder = $data['sort_order'] ?? 'desc';
    $search = $data['search'] ?? '';
    $deleted = $data['deleted'] ?? false;
    $filter = $data['filter'] ?? [];
    $filter['parent_category_id'] = $isParent ? '__null__' : '__not_null__';

    $query = $this->categoryRepository->queryFilter($filter, $search);
    $query->orderBy($sortBy, $sortOrder);

    if ($deleted) $query->onlyTrashed();

    return $query->paginate($perPage, ['*'], 'page', $page);
  }

  public function findById($id, $errorMessage = "Data not found.")
  {
    try {
      return $this->categoryRepository->findById($id);
    } catch (ModelNotFoundException $e) {
      throw new ServiceException($errorMessage, 404, $e);
    }
  }

  public function create($data, $isParent = true)
  {
    if (!$isParent && isset($data['parent_category_id'])) {
      $this->findById($data['parent_category_id'], 'Parent category not found.');
    }
    return $this->categoryRepository->create($data);
  }

  public function update($id, $data, $isParent = true)
  {
    try {
      if (!$isParent && isset($data['parent_category_id'])) {
        $this->findById($data['parent_category_id'], 'Parent Category not found.');
      }

      return $this->categoryRepository->update($id, $data);
    } catch (ModelNotFoundException $e) {
      throw new ServiceException('Data not found.', 404, $e);
    }
  }

  public function delete($id)
  {
    $category = $this->findById($id);
    if ($category->subCategories->count() > 0) {
      throw new ServiceException('Category has sub categories.', 422);
    }
    return $this->categoryRepository->delete($id);
  }

  // public function getSubCategory($parentId)
  // {
  //   return $this->categoryRepository->getSubCategory($parentId);
  // }

  // public function getParentCategory($childId)
  // {
  //   return $this->categoryRepository->getParentCategory($childId);
  // }
}
