<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\interfaces\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{

  public function __construct(Category $model)
  {
    parent::__construct($model);
  }

  public function getSubCategory($parentId)
  {
    return $this->model->where('id', $parentId)->firstOrFail()->subCategories;
  }

  // public function getParentCategory($childId)
  // {
  //   return $this->model->where('id', $childId)->first()->parentCategory;
  // }
}
