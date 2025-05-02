# 🎓 Educ-Map

A comprehensive education mapping application built with Laravel to visualize and explore educational institutions, programs, and resources. This platform helps students, educators, and administrators to navigate the educational landscape with ease.

[![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-blue.svg)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14-blue.svg)](https://postgresql.org)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## 📋 Requirements

Before you begin, ensure you have the following installed:

- 🐘 PHP 8.4 or higher
- 🧰 Composer
- 🗄️ PostgreSQL 14
- 🌐 Nginx (for production)
- 📦 Node.js and NPM (for frontend assets)

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

- Enable Xdebug in Lando:
  ```bash
  lando xdebug-on
  ```

- Disable Xdebug in Lando:
  ```bash
  lando xdebug-off
  ```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
