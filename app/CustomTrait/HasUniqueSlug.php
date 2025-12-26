<?php

namespace App\CustomTrait;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
  public static function bootHasUniqueSlug()
  {
    static::creating(function ($model) {
      $model->slug = $model->generateUniqueSlug($model->name);
    });

    static::updating(function ($model) {
      $model->slug = $model->generateUniqueSlug($model->name);
    });
  }

  private function generateUniqueSlug($name)
  {
    $baseSlug = Str::slug($name);
    $slug = $baseSlug;
    $i = 1;

    while (static::withTrashed()->where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
      $slug = $baseSlug . '-' . $i++;
    }

    return $slug;
  }
}
