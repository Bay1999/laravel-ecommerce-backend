<?php

namespace App\Services;

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

    return $query->paginate($perPage, ['*'], 'page', $data['page']);
  }

  public function findById($id)
  {
    return $this->categoryRepository->findById($id);
  }

  public function create($data)
  {
    return $this->categoryRepository->create($data);
  }

  public function update($id, $data)
  {
    return $this->categoryRepository->update($id, $data);
  }

  public function delete($id)
  {
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
