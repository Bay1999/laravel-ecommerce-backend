# Laravel E-Commerce Backend API

A robust and scalable RESTful API for e-commerce applications built with Laravel 12, implementing clean architecture principles and industry-standard design patterns.

## 🚀 Tech Stack

### Core Framework

-   **PHP** `^8.2` - Modern PHP with type safety and performance improvements
-   **Laravel Framework** `^12.0` - Latest Laravel with enhanced features
-   **MySQL/PostgreSQL** - Relational database management

### Authentication & Security

-   **JWT Authentication** (`php-open-source-saver/jwt-auth ^2.8`) - Stateless token-based authentication
-   **Laravel Sanctum** `^4.0` - API token authentication for SPAs

### Development & Testing

-   **Pest PHP** `^4.2` - Modern testing framework with elegant syntax
-   **Laravel Pint** `^1.24` - Opinionated PHP code style fixer
-   **Laravel Pail** `^1.2.2` - Real-time log monitoring
-   **Faker** `^1.23` - Generate fake data for testing and seeding

### Additional Tools

-   **Laravel Tinker** `^2.10.1` - Powerful REPL for Laravel
-   **Laravel Sail** `^1.41` - Docker development environment

## 🏗️ Architecture & Design Patterns

This project follows **Clean Architecture** principles with a layered approach:

### 1. Repository Pattern

Abstracts data access logic and provides a clean API for data operations.

```
app/Repositories/
├── interfaces/
│   └── BaseRepositoryInterface.php
├── BaseRepository.php
├── CategoryRepository.php
└── ProductRepository.php
```

**Benefits:**

-   Decouples business logic from data access
-   Easier to test and mock
-   Flexible data source switching

### 2. Service Layer Pattern

Encapsulates business logic and orchestrates operations between controllers and repositories.

```
app/Services/
├── CategoryService.php
└── ProductService.php
```

**Benefits:**

-   Single Responsibility Principle
-   Reusable business logic
-   Cleaner controllers

### 3. Custom Traits

Reusable functionality across models using PHP traits.

```
app/CustomTrait/
└── HasUniqueSlug.php
```

**Features:**

-   Automatic slug generation from names
-   Unique slug enforcement with soft-deleted records
-   Auto-incremented suffixes for duplicates

### 4. Exception Handling

Custom exception classes for better error handling and API responses.

```
app/Exceptions/
└── ServiceException.php
```

### 5. API Versioning

Routes are versioned to maintain backward compatibility.

```
/api/v1/*
```

## 📁 Project Structure

```
app/
├── CustomTrait/          # Reusable traits
├── Exceptions/           # Custom exception classes
├── Http/
│   └── Controllers/
│       └── v1/           # API v1 controllers
│           ├── Auth/     # Authentication controllers
│           ├── CategoryController.php
│           └── ProductController.php
├── Models/               # Eloquent models
│   ├── Category.php
│   ├── Product.php
│   └── User.php
├── Repositories/         # Data access layer
│   ├── interfaces/
│   └── BaseRepository.php
└── Services/             # Business logic layer
    ├── CategoryService.php
    └── ProductService.php
```

## 🔌 API Endpoints

### Authentication

```http
POST   /api/v1/register          # User registration
POST   /api/v1/login             # User login
```

### Categories (Parent Categories)

```http
GET    /api/v1/category          # Get paginated categories
POST   /api/v1/category          # Create new category
GET    /api/v1/category/{id}     # Get category by ID
PUT    /api/v1/category/{id}     # Update category
DELETE /api/v1/category/{id}     # Delete category
```

### Sub-Categories

```http
GET    /api/v1/sub-category          # Get paginated sub-categories
POST   /api/v1/sub-category          # Create new sub-category
GET    /api/v1/sub-category/{id}     # Get sub-category by ID
PUT    /api/v1/sub-category/{id}     # Update sub-category
DELETE /api/v1/sub-category/{id}     # Delete sub-category
```

### Products

```http
GET    /api/v1/product          # Get paginated products
POST   /api/v1/product          # Create new product
GET    /api/v1/product/{id}     # Get product by ID
PUT    /api/v1/product/{id}     # Update product
DELETE /api/v1/product/{id}     # Delete product
```

## 🎯 Key Features

### Advanced Querying

All list endpoints support:

-   **Pagination**: `?page=1&limit=10`
-   **Sorting**: `?sort_by=name&sort_order=asc`
-   **Search**: `?search=keyword`
-   **Soft Deletes**: `?deleted=true`

### Example Request

```http
GET /api/v1/category?page=1&limit=20&sort_by=name&sort_order=asc&search=electronics
```

### Model Features

-   **ULIDs**: Universally Unique Lexicographically Sortable Identifiers
-   **Soft Deletes**: Records are marked as deleted, not permanently removed
-   **Automatic Slugs**: SEO-friendly URLs generated automatically
-   **Timestamps**: Automatic `created_at` and `updated_at` tracking

### Validation

All create/update endpoints include comprehensive validation:

-   Required field validation
-   Type checking
-   Foreign key existence validation
-   File upload validation (images)

## 🛠️ Installation

### Prerequisites

-   PHP >= 8.2
-   Composer
-   MySQL/PostgreSQL
-   Node.js & NPM (for asset compilation)

### Setup

1. **Clone the repository**

```bash
git clone <repository-url>
cd own-backend
```

2. **Install dependencies**

```bash
composer install
npm install
```

3. **Environment configuration**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
   Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Configure JWT**

```bash
php artisan jwt:secret
```

6. **Run migrations**

```bash
php artisan migrate
```

7. **Seed database (optional)**

```bash
php artisan db:seed
```

8. **Start development server**

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 🧪 Testing

Run the test suite using Pest:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CategoryServiceTest.php

# Run with coverage
php artisan test --coverage
```

## 📝 Code Style

This project uses Laravel Pint for code formatting:

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

## 🔐 Authentication

This API uses JWT (JSON Web Tokens) for authentication.

### Login

```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Response

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### Using the Token

Include the token in the Authorization header:

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## 📊 Database Schema

### Categories Table

-   `id` (ULID, Primary Key)
-   `name` (String)
-   `slug` (String, Unique)
-   `parent_category_id` (ULID, Nullable, Foreign Key)
-   `image` (String, Nullable)
-   `created_at` (Timestamp)
-   `updated_at` (Timestamp)
-   `deleted_at` (Timestamp, Nullable)

### Relationships

-   A category can have many sub-categories
-   A sub-category belongs to one parent category

## 🚨 Error Handling

The API returns consistent error responses:

```json
{
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

### HTTP Status Codes

-   `200` - Success
-   `201` - Created
-   `400` - Bad Request
-   `401` - Unauthorized
-   `404` - Not Found
-   `422` - Unprocessable Entity (Validation Error)
-   `500` - Internal Server Error

## 📌 Important Notes for API Consumption

### PUT/PATCH Requests with Form Data

> **⚠️ IMPORTANT**: Laravel does not natively support `multipart/form-data` for PUT/PATCH requests.

**Solutions:**

1. **Method Spoofing (Recommended for file uploads)**

    ```http
    POST /api/v1/category/{id}
    Content-Type: multipart/form-data

    _method: PUT
    name: Category Name
    image: [file]
    ```

2. **Use JSON (Recommended for APIs)**

    ```http
    PUT /api/v1/category/{id}
    Content-Type: application/json

    {
      "name": "Category Name"
    }
    ```

3. **Use x-www-form-urlencoded (No file uploads)**

    ```http
    PUT /api/v1/category/{id}
    Content-Type: application/x-www-form-urlencoded

    name=Category+Name
    ```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

-   Follow PSR-12 coding standards
-   Write tests for new features
-   Update documentation as needed
-   Use meaningful commit messages

## 📄 License

This project is licensed under the MIT License.

## 👥 Authors

-   Your Name - Initial work

## 🙏 Acknowledgments

-   Laravel Community
-   PHP-Open-Source-Saver for JWT Auth
-   All contributors and supporters

---

**Built with ❤️ using Laravel**
