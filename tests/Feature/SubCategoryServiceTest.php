<?php

namespace Tests\Feature;

use App\Exceptions\ServiceException;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(RefreshDatabase::class);

$specifyData = [
  ['name' => 'Oppo', 'slug' => 'oppo', 'parent_category_id' => null],
  ['name' => 'Samsung Galaxy', 'slug' => 'samsung-galaxy', 'parent_category_id' => null],
  ['name' => 'Apple iPhone', 'slug' => 'apple-iphone', 'parent_category_id' => null],
  ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'parent_category_id' => null],
  ['name' => 'Vivo', 'slug' => 'vivo', 'parent_category_id' => null],
  ['name' => 'LG', 'slug' => 'lg', 'parent_category_id' => null, 'deleted_at' => now()],
  ['name' => 'Sharp', 'slug' => 'sharp', 'parent_category_id' => null, 'deleted_at' => now()],
];

describe('Sub Category Pagination and Filters', function () use ($specifyData) {

  it('return paginated categories', function ($isParent, $sort_by, $sort_order, $page, $search, $expectedFirstKey, $expectedHaveCount, $expectedTotal) use ($specifyData) {
    $categories = Category::factory()->count(5)->create();
    $categoriesIds = $categories->pluck('id')->toArray();
    $subCategories = Category::factory()->createMany(
      array_map(function ($data) use ($categoriesIds) {
        $data['parent_category_id'] = fake()->randomElement($categoriesIds);
        return $data;
      }, $specifyData)
    );

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $requestArray = [
      'limit' => 2,
      'page' => $page,
      'search' => $search,
      'sort_order' => $sort_order,
      'sort_by' => $sort_by,
    ];
    $result = $service->getCategoriesPaginated($requestArray, $isParent);
    expect($result->total())->toBe($expectedTotal);
    expect($result->items())->toHaveCount($expectedHaveCount);
    if ($expectedHaveCount) {
      expect(
        $result->items()[0]->{$requestArray['sort_by']}
      )->toBe(
        $subCategories[$expectedFirstKey]->{$requestArray['sort_by']}
      );
    }
  })->with([
    'page 1 active and sort by id ascending' => [
      'isParent' => false,
      'page' => 1,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'asc',
      'expectedFirstKey' => 0,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by name ascending' => [
      'isParent' => false,
      'page' => 1,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'asc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by id descending' => [
      'isParent' => false,
      'page' => 1,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by name descending' => [
      'isParent' => false,
      'page' => 1,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 3,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and search' => [
      'isParent' => false,
      'page' => 1,
      'search' => 'vivo',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 1,
      'expectedTotal' => 1,
    ],
    'page 2 active and sort by id ascending' => [
      'isParent' => false,
      'page' => 2,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'asc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by name ascending' => [
      'isParent' => false,
      'page' => 2,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'asc',
      'expectedFirstKey' => 1,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by id descending' => [
      'isParent' => false,
      'page' => 2,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'desc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by name descending' => [
      'isParent' => false,
      'page' => 2,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 1,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and search' => [
      'isParent' => false,
      'page' => 2,
      'search' => 'vivo',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 0,
      'expectedTotal' => 1,
    ],
  ]);
});

describe('Insert New Sub Category', function () use ($specifyData) {
  it('return new data sub category', function () {
    $category = Category::factory()->count(5)->create();
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $expectedData = [
      'name' => 'New Sub Category',
      'slug' => 'new-sub-category',
      'parent_category_id' => fake()->randomElement($category->pluck('id')->toArray()),
    ];

    $result = $service->create([
      'name' => $expectedData['name'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);

    assertDatabaseHas('categories', [
      'id' => $result->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);
  });

  it('return new data sub category with slug already exists', function () use ($specifyData) {
    $categories = Category::factory()->count(5)->create();
    $categoriesIds = $categories->pluck('id')->toArray();
    $subCategories = Category::factory()->createMany(
      array_map(function ($data) use ($categoriesIds) {
        $data['parent_category_id'] = fake()->randomElement($categoriesIds);
        return $data;
      }, $specifyData)
    );

    $expectedData = [
      'name' => 'Samsung Galaxy',
      'slug' => 'samsung-galaxy-1',
      'parent_category_id' => fake()->randomElement($categoriesIds),
    ];

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->create([
      'name' => $expectedData['name'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);

    assertDatabaseHas('categories', [
      'id' => $result->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);
  });

  it('return exception data sub category cant be inserted cause data parent not found', function () {
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->create([
      'name' => 'New Oppo',
      'parent_category_id' => '123123',
    ]))
      ->toThrow(ServiceException::class, 'Data not found.');
  });
});

describe('Update Sub Category', function () use ($specifyData) {
  it('return data sub category that already updated', function () use ($specifyData) {
    $categories = Category::factory()->count(5)->create();
    $categoriesIds = $categories->pluck('id')->toArray();
    $subCategories = Category::factory()->createMany(
      array_map(function ($data) use ($categoriesIds) {
        $data['parent_category_id'] = fake()->randomElement($categoriesIds);
        return $data;
      }, $specifyData)
    );

    $expectedData = [
      'name' => 'New Oppo',
      'slug' => 'new-oppo',
      'parent_category_id' => fake()->randomElement($categoriesIds),
    ];

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->update($subCategories->first()->id, [
      'name' => $expectedData['name'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);

    assertDatabaseHas('categories', [
      'id' => $subCategories->first()->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);
  });

  it('return data sub category that already updated with slug already exists', function () use ($specifyData) {
    $categories = Category::factory()->count(5)->create();
    $categoriesIds = $categories->pluck('id')->toArray();
    $subCategories = Category::factory()->createMany(
      array_map(function ($data) use ($categoriesIds) {
        $data['parent_category_id'] = fake()->randomElement($categoriesIds);
        return $data;
      }, $specifyData)
    );

    $expectedData = [
      'name' => 'Samsung Galaxy',
      'slug' => 'samsung-galaxy-1',
      'parent_category_id' => fake()->randomElement($categoriesIds),
    ];

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->update($subCategories->first()->id, [
      'name' => $expectedData['name'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);

    assertDatabaseHas('categories', [
      'id' => $subCategories->first()->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);
  });

  it('return exception data sub category cant be edited cause data parent not found', function () {
    $dataCategory = Category::factory()->create(
      [
        'name' => 'Samsung Galaxy',
        'slug' => 'samsung-galaxy-1',
        'parent_category_id' => '123123'
      ]
    );

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->update($dataCategory->id, [
      'name' => 'New Oppo',
      'parent_category_id' => '123123',
    ]))
      ->toThrow(ServiceException::class, 'Category not found.');
  });
});

describe('Delete Category', function () use ($specifyData) {
  it('return data category that already deleted', function () use ($specifyData) {
    $parent = Category::factory()->createMany($specifyData);

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->delete($parent->first()->id);

    assertSoftDeleted('categories', [
      'id' => $parent->first()->id,
    ]);
  });


  it('return exception data category to delete not found', function () {
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->delete('12312323'))
      ->toThrow(ServiceException::class, 'Data not found.');
  });
});
