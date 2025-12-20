<?php

namespace App\Repositories\interfaces;

interface BaseRepositoryInterface
{
  public function all();
  public function queryFilter($filter = [], $search = []);
  public function create($data);
  public function findById($id);
  public function update($id, $data);
  public function delete($id);
}
