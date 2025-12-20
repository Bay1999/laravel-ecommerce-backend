<?php

namespace App\Repositories;

use App\Repositories\interfaces\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
  protected $model;

  public function __construct($model)
  {
    $this->model = $model;
  }

  public function all()
  {
    return $this->model->all();
  }

  public function queryFilter($filter = [], $search = [])
  {
    $query = $this->model->query();

    if (count($filter) > 0) {
      foreach ($filter as $key => $value) {
        $query->where($key, $value);
      }
    }

    if (count($search) > 0) {
      $query->where(function ($q) use ($search) {
        foreach ($search as $key => $value) {
          $q->orWhere($key, 'like', '%' . $value . '%');
        }
      });
    }

    return $query;
  }

  public function create($data)
  {
    return $this->model->create($data);
  }

  public function findById($id)
  {
    return $this->model->find($id);
  }

  public function update($id, $data)
  {
    return $this->model->update($id, $data);
  }

  public function delete($id)
  {
    return $this->model->delete($id);
  }
}
