<?php

use App\Http\Controllers\v1\Auth\AuthController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
  Route::post("/register", [AuthController::class, "register"]);
  Route::post("/login", [AuthController::class, "login"]);

  Route::prefix("/category")->group(function () {
    Route::get("/", [CategoryController::class, "getCategoriesPaginated"]);
    Route::post("/", [CategoryController::class, "createCategory"]);
    Route::get("/{id}", [CategoryController::class, "getCategoryById"]);
    Route::put("/{id}", [CategoryController::class, "updateCategory"]);
    Route::delete("/{id}", [CategoryController::class, "deleteCategory"]);
  });

  Route::prefix("/sub-category")->group(function () {
    Route::get("/", [CategoryController::class, "getSubCategoriesPaginated"]);
    Route::post("/", [CategoryController::class, "createSubCategory"]);
    Route::get("/{id}", [CategoryController::class, "getSubCategoryById"]);
    Route::put("/{id}", [CategoryController::class, "updateSubCategory"]);
    Route::delete("/{id}", [CategoryController::class, "deleteSubCategory"]);
  });

  Route::prefix("/product")->group(function () {
    Route::get("/", [ProductController::class, "getProductsPaginated"]);
    Route::post("/", [ProductController::class, "createProduct"]);
    Route::get("/{id}", [ProductController::class, "getProductById"]);
    Route::put("/{id}", [ProductController::class, "updateProduct"]);
    Route::delete("/{id}", [ProductController::class, "deleteProduct"]);
  });
});
