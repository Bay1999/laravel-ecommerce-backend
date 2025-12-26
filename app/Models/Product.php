<?php

namespace App\Models;

use App\CustomTrait\HasUniqueSlug;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  /** @use HasFactory<\Database\Factories\ProductFactory> */
  use HasFactory, HasUlids, HasUniqueSlug, SoftDeletes;

  protected $fillable = [
    'name',
    'slug',
    'sub_category_id',
    'description',
    'price',
  ];


  public $filterable = [
    'name',
    'slug',
    'price',
  ];

  public $searchable = [
    'name',
    'slug',
    'description',
    'price',
  ];

  public $sortable = [
    'name',
    'slug',
    'sub_category_id',
    'description',
    'price',
  ];

  public function subCategory()
  {
    return $this->belongsTo(Category::class, 'id', 'sub_category_id');
  }
}
