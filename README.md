# 🎓 Educ-Map

A comprehensive education mapping application built with Laravel to visualize and explore educational institutions, programs, and resources. This platform helps students, educators, and administrators to navigate the educational landscape with ease.

[![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-blue.svg)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14-blue.svg)](https://postgresql.org)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## 📋 Requirements

Before you begin, ensure you have the following installed:

-   🐘 PHP 8.4 or higher
-   🧰 Composer
-   🗄️ PostgreSQL 14
-   🌐 Nginx (for production)
-   📦 Node.js and NPM (for frontend assets)

## 🚀 Installation

### Manual Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/educ-map.git
    cd educ-map
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Copy environment file and generate application key**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configure the environment variables**

    Update the `.env` file with your database credentials:

    ```
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=educ_map
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

5. **Run database migrations and seeders**

    ```bash
    php artisan migrate --seed
    ```

6. **Install and compile frontend assets**

    ```bash
    npm install
    npm run build
    ```

7. **Start the development server**

    ```bash
    php artisan serve
    ```

    Access the application at: [http://localhost:8000](http://localhost:8000)

### 🐳 Installation with Lando

1. **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/educ-map.git
    cd educ-map
    ```

2. **Start Lando**

    ```bash
    lando start
    ```

3. **Install PHP dependencies**

    ```bash
    lando composer install
    ```

4. **Copy environment file and generate application key**

    ```bash
    cp .env.example .env
    lando artisan key:generate
    ```

5. **Update the environment variables**

    Configure the `.env` file for Lando:

    ```
    DB_CONNECTION=pgsql
    DB_HOST=database
    DB_PORT=5432
    DB_DATABASE=educ_map
    DB_USERNAME=postgres
    DB_PASSWORD=postgres
    ```

6. **Run database migrations and seeders**

    ```bash
    lando artisan migrate --seed
    ```

7. **Install and compile frontend assets**

    ```bash
    lando npm install
    lando npm run build
    ```

    Access the application at the URL provided by Lando after startup.

## 📚 API Documentation

Educ-Map provides a comprehensive API for integrating with the platform. The API documentation is generated using [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) based on [OpenAPI 3.0](https://swagger.io/specification/) specifications.

### 📖 Accessing API Documentation

Access the interactive API documentation at:

```
https://your-domain.com/api/docs
```

Or if using Lando:

```
https://educ-map.lndo.site/api/docs
```

The documentation provides a user-friendly interface to explore all available endpoints, including:

-   Authentication (login, register, profile management)
-   Establishments (listing, filtering, detailed information)
-   Map data (for marker generation)
-   Comparison functionality
-   Search history management

### 🔑 Authentication

The API uses Laravel Sanctum for token-based authentication. To make authenticated requests:

1. Obtain a token by calling the login endpoint:

    ```
    POST /api/login
    ```

2. Include the token in subsequent requests using the Authorization header:
    ```
    Authorization: Bearer {your_token}
    ```

### 🏗️ API Documentation Structure

The OpenAPI documentation is organized using the following structure:

1. **Base API Information**

    The `/app/OpenApi/OpenApiSpec.php` file contains the base information for the API, including:

    - API title, version, and description
    - Global security schemes (Bearer Authentication)
    - Contact information
    - License details
    - API tags for grouping endpoints

2. **Schema Definitions**

    Reusable schema components are defined in separate files in the `/app/OpenApi/Schemas` directory:

    - `EstablishmentResourceSchema.php` - Basic establishment list data
    - `EstablishmentDetailResourceSchema.php` - Detailed establishment information
    - `MapMarkerSchema.php` - Lightweight map marker data
    - `UserSchema.php` - User profile information
    - `SearchHistorySchema.php` - Stored search queries

3. **Controller Annotations**

    Each API controller includes OpenAPI annotations that define:

    - Endpoint paths and HTTP methods
    - Summary and detailed descriptions
    - Request parameters (path, query, body)
    - Response formats and status codes
    - Authentication requirements
    - Tags for logical grouping

### ⚙️ API Configuration

To customize the API documentation, you can modify the L5-Swagger configuration:

1. **Environment Variables**

    Add the following to your `.env` file to control the behavior:

    ```
    L5_SWAGGER_GENERATE_ALWAYS=true  # Auto-generate docs on each request (dev only)
    L5_SWAGGER_CONST_HOST=https://your-domain.com  # API base URL
    L5_SWAGGER_UI_DOC_EXPANSION=list  # Control UI expansion (list, full, none)
    L5_SWAGGER_UI_ASSETS_PATH=vendor/swagger-api/swagger-ui/dist/
    L5_FORMAT_TO_USE_FOR_DOCS=json
    L5_SWAGGER_USE_ABSOLUTE_PATH=true
    ```

2. **Configuration File**

    The primary configuration file is located at `config/l5-swagger.php` and controls:

    - Paths where OpenAPI annotations are scanned
    - Security definition settings
    - Documentation generation options
    - UI customization options

3. **Regenerate Documentation**

    After making changes to API annotations, regenerate the documentation:

    ```bash
    php artisan l5-swagger:generate
    ```

### 📝 OpenAPI Annotation Examples

1. **Controller Class Annotation**

    ```php
    /**
     * @OA\Get(
     *     path="/api/establishments",
     *     summary="Get list of establishments",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Results per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of establishments",
     *         @OA\JsonContent(ref="#/components/schemas/EstablishmentResourceSchema")
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Controller method implementation
    }
    ```

2. **Schema Definition**

    ```php
    /**
     * @OA\Schema(
     *     schema="User",
     *     title="User Schema",
     *     description="User model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *     @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"ROLE_USER"}),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
    class UserSchema
    {
    }
    ```

### 🔍 Testing API Endpoints

1. **Using Swagger UI**

    The interactive Swagger UI at `/api/docs` allows you to:

    - Explore all available endpoints
    - Test endpoints directly from the browser
    - View request/response schemas
    - Authorize with a Bearer token for protected endpoints

2. **Using Postman**

    A Postman collection is available for testing API endpoints:

    ```bash
    # Import the collection into Postman
    educ-map-postman-collection.json
    ```

3. **Using cURL**

    Example of calling an API endpoint with cURL:

    ```bash
    # Get list of establishments
    curl -X GET "https://educ-map.lndo.site/api/establishments" -H "Accept: application/json"

    # Login and get token
    curl -X POST "https://educ-map.lndo.site/api/login" \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d '{"email":"user@example.com", "password":"password"}'
    ```

## 🛠️ Usage

### 🔧 Artisan Commands

```bash
# Using Lando
lando artisan list

# Without Lando
php artisan list
```

### 🧪 Running Tests

```bash
# Using Lando
lando artisan test

# Without Lando
php artisan test
```

### 🔄 Database Operations

```bash
# Using Lando
lando artisan migrate:fresh --seed

# Access PostgreSQL CLI
lando psql
```

### 🐞 Debugging

-   Enable Xdebug in Lando:

    ```bash
    lando xdebug-on
    ```

-   Disable Xdebug in Lando:
    ```bash
    lando xdebug-off
    ```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
