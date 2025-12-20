<?php

namespace App\CustomTrait;

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

    while (static::where('slug', $slug)->exists()) {
      $slug = $baseSlug . '-' . $i++;
    }

    return $slug;
  }
}
