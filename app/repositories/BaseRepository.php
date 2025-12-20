<?php

namespace App\Repositories;

use App\Repositories\interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;

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
      $filterable = $this->model->filterable ?? [];
      foreach ($filter as $key => $value) {
        if (in_array($key, $filterable)) {
          if ($value === '__not_null__') $query->whereNotNull($key);
          else if ($value === '__null__') $query->whereNull($key);
          else $query->where($key, $value);
        }
      }
    }

    if ($search) {
      $searchable = $this->model->searchable;
      $query->where(function ($q) use ($search, $searchable) {
        foreach ($searchable as $key => $value) {
          $q->orWhere($value, 'like', '%' . $search . '%');
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
    return $this->model->findOrFail($id);
  }

  public function findBySlug($slug)
  {
    return $this->model->where('slug', $slug)->first();
  }

  public function update($id, $data)
  {
    return tap($this->model->findOrFail($id))->update($data);
  }

  public function delete($id)
  {
    return tap($this->model->findOrFail($id))->delete();
  }
}
