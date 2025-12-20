<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Exception;

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
    $perPage = $data['limit'] ?? 10;
    $sortBy = $data['sort_by'] ?? 'id';
    $sortOrder = $data['sort_order'] ?? 'desc';
    $search = $data['search'] ?? [];
    $deleted = $data['deleted'] ?? false;
    $filter = $data['filter'] ?? [];
    $filter['parent_category_id'] = $isParent ? null : '__not_null__';

    $query = $this->categoryRepository->queryFilter($filter, $search);
    $query->orderBy($sortBy, $sortOrder);

    if ($deleted) $query->onlyTrashed();

    return $query->paginate($perPage);
  }

  public function findById($id)
  {
    $category = $this->categoryRepository->findById($id);
    if (!$category) {
      throw new Exception("Category not found");
    }
    return $category;
  }

  public function create($data)
  {
    return $this->categoryRepository->create($data);
  }

  public function update($id, $data)
  {
    $category = $this->findById($id);
    return $this->categoryRepository->update($category->id, $data);
  }

  public function delete($id)
  {
    $category = $this->findById($id);
    return $this->categoryRepository->delete($category->id);
  }

  public function getSubCategory($parentId)
  {
    $category = $this->categoryRepository->getSubCategory($parentId);
    if (!$category) {
      throw new Exception("Category not found");
    }
    return $category;
  }

  public function getParentCategory($childId)
  {
    $category = $this->findById($childId);
    if (!$category) {
      throw new Exception("Category not found");
    }
    return $this->categoryRepository->getParentCategory($category->id);
  }
}
