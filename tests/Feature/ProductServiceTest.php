<?php

namespace Tests\Feature;

use App\Exceptions\ServiceException;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;


uses(RefreshDatabase::class);


$categoryData = [
  ['name' => 'Tablet', 'slug' => 'tablet', 'parent_category_id' => null],
  ['name' => 'Smartphone', 'slug' => 'smartphone', 'parent_category_id' => null],
];

$subCategory = [
  ['name' => 'Gaming', 'slug' => 'tablet', 'parent_category_id' => null],
  ['name' => 'Working', 'slug' => 'smartphone', 'parent_category_id' => null, 'deleted_at' => now()],
];

$productData = [
  ['name' => 'Oppo', 'slug' => 'oppo'],
  ['name' => 'Samsung Galaxy', 'slug' => 'samsung-galaxy'],
  ['name' => 'Apple iPhone', 'slug' => 'apple-iphone'],
  ['name' => 'Xiaomi', 'slug' => 'xiaomi'],
  ['name' => 'Vivo', 'slug' => 'vivo'],
  ['name' => 'LG', 'slug' => 'lg', 'deleted_at' => now()],
  ['name' => 'Sharp', 'slug' => 'sharp', 'deleted_at' => now()],
];

describe('Product Pagination and Filters', function () use ($categoryData, $productData, $subCategory) {
  it('return all products data', function () use ($categoryData, $productData, $subCategory) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );


    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);
    $result = $service->getAll();

    expect($result->count())->toBe(5);
  });

  it('return paginated products', function ($sort_by, $sort_order, $page, $search, $expectedFirstKey, $expectedHaveCount, $expectedTotal) use ($productData, $categoryData, $subCategory) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );

    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $requestArray = [
      'limit' => 2,
      'page' => $page,
      'search' => $search,
      'sort_order' => $sort_order,
      'sort_by' => $sort_by,
    ];
    $result = $service->getProductsPaginated($requestArray);
    expect($result->total())->toBe($expectedTotal);
    expect($result->items())->toHaveCount($expectedHaveCount);
    if ($expectedHaveCount) {
      expect(
        $result->items()[0]->{$requestArray['sort_by']}
      )->toBe(
        $productData[$expectedFirstKey]->{$requestArray['sort_by']}
      );
    }
  })->with([
    'page 1 active and sort by id ascending' => [
      'page' => 1,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'asc',
      'expectedFirstKey' => 0,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by name ascending' => [
      'page' => 1,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'asc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by id descending' => [
      'page' => 1,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and sort by name descending' => [
      'page' => 1,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 3,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 1 active and search' => [
      'page' => 1,
      'search' => 'vivo',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 4,
      'expectedHaveCount' => 1,
      'expectedTotal' => 1,
    ],
    'page 2 active and sort by id ascending' => [
      'page' => 2,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'asc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by name ascending' => [
      'page' => 2,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'asc',
      'expectedFirstKey' => 1,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by id descending' => [
      'page' => 2,
      'search' => '',
      'sort_by' => 'id',
      'sort_order' => 'desc',
      'expectedFirstKey' => 2,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and sort by name descending' => [
      'page' => 2,
      'search' => '',
      'sort_by' => 'name',
      'sort_order' => 'desc',
      'expectedFirstKey' => 1,
      'expectedHaveCount' => 2,
      'expectedTotal' => 5,
    ],
    'page 2 active and search' => [
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


describe('Insert New Product', function () use ($productData, $subCategory) {
  it('return new data product', function () use ($productData) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);

    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $expectedData = [
      'name' => 'New Product',
      'slug' => 'new-product',
      'sub_category_id' => $subCategory->filter(function ($cat) {
        return !is_null($cat->parent_category_id);
      })->random()->id,
    ];

    $result = $service->create([
      'name' => $expectedData['name'],
      'sub_category_id' => $expectedData['sub_category_id'],
    ]);

    assertDatabaseHas('products', [
      'id' => $result->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'sub_category_id' => $expectedData['sub_category_id'],
    ]);
  });

  it('return new data product with slug already exists', function () use ($productData, $subCategory) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );

    // $dataDuplicate = $productData->random();
    $dataDuplicate = $productData->first();
    $expectedData = [
      'name' => $dataDuplicate->name,
      'slug' => $dataDuplicate->slug . '-1',
      'sub_category_id' => $dataDuplicate->sub_category_id,
    ];

    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $result = $service->create([
      'name' => $expectedData['name'],
      'sub_category_id' => $expectedData['sub_category_id'],
    ]);

    assertDatabaseHas('products', [
      'id' => $result->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
    ]);
  });

  it('return exception causes sub category not found', function () use ($productData) {
    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $expectedData = [
      'name' => 'New Product',
      'sub_category_id' => '123123123',
    ];

    expect(fn() => $service->create($expectedData))
      ->toThrow(ServiceException::class, ' category not found.');
  });

  it('return exception causes category selected must be a sub category', function () use ($productData) {
    $parentCategory = Category::factory()->count(5)->create();

    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $expectedData = [
      'name' => 'New Product',
      'sub_category_id' => $parentCategory->random()->id,
    ];

    expect(fn() => $service->create($expectedData))
      ->toThrow(ServiceException::class, 'Category selected must be a sub category.');
  });
});

describe('Update Product', function () use ($productData, $subCategory) {
  it('return updated data product', function () use ($productData) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );

    // $productData = $productData->random();
    $productData = $productData->first();
    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $expectedData = [
      'name' => 'New Product',
      'slug' => 'new-product',
      'sub_category_id' => $subCategory->filter(function ($cat) {
        return !is_null($cat->parent_category_id);
      })->random()->id,
    ];

    $result = $service->update($productData->id, [
      'name' => $expectedData['name'],
      'sub_category_id' => $expectedData['sub_category_id'],
    ]);

    assertDatabaseHas('products', [
      'id' => $productData->id,
      'name' => $expectedData['name'],
      'slug' => $expectedData['slug'],
      'sub_category_id' => $expectedData['sub_category_id'],
    ]);
  });

  it('return exception Data not found', function () use ($productData) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );

    $productData = $productData->first();
    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $expectedData = [
      'name' => 'New Product',
      'slug' => 'new-product',
      'sub_category_id' => $subCategory->filter(function ($cat) {
        return !is_null($cat->parent_category_id);
      })->random()->id,
    ];

    expect(fn() => $service->update('asd123123s1', [
      'name' => $expectedData['name'],
      'sub_category_id' => $expectedData['sub_category_id'],
    ]))
      ->toThrow(ServiceException::class, 'Data not found.');
  });

  it('return exception causes sub category not found', function () use ($productData) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );

    $productData = $productData->first();
    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);

    $expectedData = [
      'name' => 'New Product',
      'slug' => 'new-product',
      'sub_category_id' => '123123123',
    ];

    expect(fn() => $service->update($productData->id, $expectedData))
      ->toThrow(ServiceException::class, 'Sub category not found.');
  });

  it('return exception causes category selected must be a sub category', function () use ($productData) {
    $parentCategory = Category::factory()->count(5)->create();
    $subCategory = Category::factory()->count(5)->create([
      'parent_category_id' => $parentCategory->random()->id
    ]);
    $productData = Product::factory()->createMany(
      array_map(function ($data) use ($subCategory) {
        $data['sub_category_id'] = $subCategory->random()->id;
        return $data;
      }, $productData)
    );

    $productData = $productData->first();
    $repository = new ProductRepository(new Product());
    $categoryService = new CategoryService(new CategoryRepository(new Category()));
    $service = new ProductService($repository, $categoryService);


    $expectedData = [
      'name' => 'New Product',
      'slug' => 'new-product',
      'sub_category_id' => $parentCategory->random()->id,
    ];

    expect(fn() => $service->update($productData->id, $expectedData))
      ->toThrow(ServiceException::class, 'Category selected must be a sub category.');
  });
});
