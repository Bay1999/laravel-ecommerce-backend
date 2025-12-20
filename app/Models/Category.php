<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  /** @use HasFactory<\Database\Factories\CategoryFactory> */
  use HasFactory, HasUlids, SoftDeletes;

  protected $fillable = [
    'name',
    'slug',
    'parent_category_id',
    'image',
  ];

  protected $filterable = [
    'name',
    'slug',
    'parent_category_id',
    'image',
  ];

  protected $searchable = [
    'name',
    'slug',
    'parent_category_id',
  ];

  protected $sortable = [
    'id',
    'name',
    'parent_category_id',
  ];

  public function parentCategory()
  {
    return $this->belongsTo(Category::class, 'parent_category_id', 'id');
  }

  public function subCategories()
  {
    return $this->hasMany(Category::class, 'id', 'parent_category_id');
  }
}
