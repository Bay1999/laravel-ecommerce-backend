<?php

namespace App\Models;

use App\CustomTrait\HasUniqueSlug;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  /** @use HasFactory<\Database\Factories\CategoryFactory> */
  use HasFactory, HasUlids, SoftDeletes, HasUniqueSlug;

  protected $fillable = [
    'name',
    'slug',
    'parent_category_id',
    'image',
  ];

  public $filterable = [
    'name',
    'slug',
    'parent_category_id',
    'image',
  ];

  public $searchable = [
    'name',
    'slug',
    'parent_category_id',
  ];

  public $sortable = [
    'id',
    'name',
    'parent_category_id',
  ];

  public function parentCategory()
  {
    return $this->belongsTo(Category::class, 'id', 'parent_category_id');
  }

  public function subCategories()
  {
    return $this->hasMany(Category::class, 'parent_category_id', 'id');
  }
}
