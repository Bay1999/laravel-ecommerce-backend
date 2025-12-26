<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{

  function __construct(Product $model)
  {
    parent::__construct($model);
  }
}
