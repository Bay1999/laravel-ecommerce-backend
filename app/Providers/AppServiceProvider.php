<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\interfaces\BaseRepositoryInterface;
use App\Repositories\interfaces\CategoryRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->bind(BaseRepositoryInterface::class, UserRepository::class);
    $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
