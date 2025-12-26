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

describe('Category Pagination and Filters', function () use ($specifyData) {
  it('return all categories data', function () use ($specifyData) {
    $parents = Category::factory()->count(3)->create(['parent_category_id' => null]);
    $parentId = $parents->first()->id;
    Category::factory()->count(2)->create([
      'parent_category_id' => $parentId
    ]);

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->getAll();

    expect($result->count())->toBe(5);
  });

  it('return paginated categories', function ($isParent, $sort_by, $sort_order, $page, $search, $expectedFirstKey, $expectedHaveCount, $expectedTotal) use ($specifyData) {
    $parents = Category::factory()->createMany($specifyData);
    $parentId = $parents->first()->id;
    Category::factory()->count(3)->create([
      'parent_category_id' => $parentId
    ]);

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
        $parents[$expectedFirstKey]->{$requestArray['sort_by']}
      );
    }
  })->with([
    'page 1 active and sort by id ascending' => [
      'isParent' => true,
      'page' => 1,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'asc',
      'expectedFirstKey' => 0,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by name ascending' => [
      'isParent' => true,
      'page' => 1,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'asc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by id descending' => [
      'isParent' => true,
      'page' => 1,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by name descending' => [
      'isParent' => true,
      'page' => 1,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 3,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and search' => [
      'isParent' => true,
      'page' => 1,
      'search' => 'vivo',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 1,
      'expectedTotal' => 1,
    ],
    'page 2 active and sort by id ascending' => [
      'isParent' => true,
      'page' => 2,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'asc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by name ascending' => [
      'isParent' => true,
      'page' => 2,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'asc',
      'expectedFirstKey' => 1,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by id descending' => [
      'isParent' => true,
      'page' => 2,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'desc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by name descending' => [
      'isParent' => true,
      'page' => 2,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 1,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and search' => [
      'isParent' => true,
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

describe('Get Category By ID', function () use ($specifyData) {
  it("return data category by id", function () use ($specifyData) {
    $parent = Category::factory()->createMany($specifyData);
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parent->first()->id
    ]);

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->findById($parent->first()->id);
    expect($result->is($parent->first()))->toBeTrue();
  });

  it('return exception causes by id not found', function () {
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->findById('12312323'))
      ->toThrow(ServiceException::class, 'Data not found.');
  });
});

describe('Insert New Category', function () use ($specifyData) {
  it('return new data category', function () {
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $expectedData = [
      'name' => 'New Category',
      'slug' => 'new-category',
      'parent_category_id' => null,
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

  it('return new data category with slug already exists', function () use ($specifyData) {
    $parent = Category::factory()->createMany($specifyData);

    $expectedData = [
      'name' => 'Samsung Galaxy',
      'slug' => 'samsung-galaxy-1',
      'parent_category_id' => null,
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
});

describe('Update Category', function () use ($specifyData) {
  it('return data category that already updated', function () use ($specifyData) {
    $parent = Category::factory()->createMany($specifyData);

    $expectedData = [
      'name' => 'New Oppo',
      'slug' => 'new-oppo',
      'parent_category_id' => null,
    ];

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->update($parent->first()->id, [
      'name' => $expectedData['name'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);

    assertDatabaseHas('categories', [
      'id' => $parent->first()->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);
  });

  it('return data category that already updated with slug already exists', function () use ($specifyData) {
    $parent = Category::factory()->createMany($specifyData);

    $expectedData = [
      'name' => 'Samsung Galaxy',
      'slug' => 'samsung-galaxy-1',
      'parent_category_id' => null,
    ];

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    $result = $service->update($parent->first()->id, [
      'name' => $expectedData['name'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);

    assertDatabaseHas('categories', [
      'id' => $parent->first()->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'parent_category_id' => $expectedData['parent_category_id'],
    ]);
  });

  it('return exception Data not found', function () use ($specifyData) {
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->update('12312323', [
      'name' => 'New Oppo',
      'parent_category_id' => null,
    ]))
      ->toThrow(ServiceException::class, 'Data not found.');
  });

  it('return exception data category cant be edited cause data is deleted', function () use ($specifyData) {
    $dataCategory = Category::factory()->create(
      [
        'name' => 'Samsung Galaxy',
        'slug' => 'samsung-galaxy-1',
        'parent_category_id' => null,
        'deleted_at' => now(),
      ]
    );

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->update($dataCategory->id, [
      'name' => 'New Oppo',
      'parent_category_id' => null,
    ]))
      ->toThrow(ServiceException::class, 'Data not found.');
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


  it('return exception cause data category to delete not found', function () {
    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->delete('12312323'))
      ->toThrow(ServiceException::class, 'Data not found.');
  });

  it('return exception cause data category already use on sub category', function () use ($specifyData) {
    $parent = Category::factory()->createMany($specifyData);
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parent->first()->id
    ]);

    $repository = new CategoryRepository(new Category());
    $service = new CategoryService($repository);

    expect(fn() => $service->delete($parent->first()->id))
      ->toThrow(ServiceException::class, 'Category has sub categories.');
  });
});
