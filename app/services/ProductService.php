<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ProductService
{

  protected $productRepository;
  protected $categoryService;

  public function __construct(ProductRepository $productRepository, CategoryService $categoryService)
  {
    $this->productRepository = $productRepository;
    $this->categoryService = $categoryService;
  }

  public function getAll()
  {
    return $this->productRepository->all();
  }

  public function getProductsPaginated($data)
  {
    $perPage = $data['limit'] ?? 10;
    $sortBy = $data['sort_by'] ?? 'id';
    $sortOrder = $data['sort_order'] ?? 'desc';
    $search = $data['search'] ?? '';
    $deleted = $data['deleted'] ?? false;
    $filter = [];

    $query = $this->productRepository->queryFilter($filter, $search);
    $query->orderBy($sortBy, $sortOrder);

    if ($deleted) $query->onlyTrashed();

    return $query->paginate($perPage, ['*'], 'page', $data['page']);
  }

  public function findById($id, $errorMessage = "Data not found.")
  {
    try {
      return $this->productRepository->findById($id);
    } catch (ModelNotFoundException $e) {
      throw new ServiceException($errorMessage, 404, $e);
    }
  }


  public function create($data)
  {
    $subCategory = $this->categoryService->findById($data['sub_category_id'], "Sub category not found.");
    if ($subCategory->parent_category_id === null) {
      throw new ServiceException('Category selected must be a sub category.', 422);
    }
    return $this->productRepository->create($data);
  }

  public function update($id, $data)
  {
    try {
      $subCategory = $this->categoryService->findById($data['sub_category_id'], "Sub category not found.");
      if ($subCategory->parent_category_id === null) {
        throw new ServiceException('Category selected must be a sub category.', 422);
      }
      return $this->productRepository->update($id, $data);
    } catch (ModelNotFoundException $e) {
      throw new ServiceException('Data not found.', 404, $e);
    }
  }

  public function delete($id)
  {
    $product = $this->findById($id);
    return $this->productRepository->delete($id);
  }
}
