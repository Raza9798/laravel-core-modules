**laravel-core-modules** package.

---

# 📦 Laravel Core Modules

A lightweight Laravel package to generate and manage **modular application structure** using Artisan-based scaffolding.

It helps you organize Laravel applications into feature-based modules containing:

* Controllers
* Models
* Migrations
* Seeders
* Factories
* Requests
* Policies

---

# 🚀 Installation

## 1. Install via Composer

```bash
$ composer require raza9798/laravel-core-modules
$ php artisan core-modules:install
```

---

## 2. Register Service Provider (if auto-discovery is disabled)

```php
// config/app.php

'providers' => [
    Raza9798\LaravelCoreModules\CoreModulesServiceProvider::class,
],
```

---

## 3. Setup Modules Autoload (IMPORTANT)

Add this to your Laravel application's `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "Modules/"
    }
}
```

Then run:

```bash
composer dump-autoload
```

---

# ⚙️ Configuration (Optional)

Publish config file:

```bash
php artisan vendor:publish --tag=laravel-core-modules-config
```

---

# 📁 Default Module Structure

When a module is created:

```
Modules/
└── Inventory/
    ├── app/
    │   ├── Http/
    │   │   └── Controllers/
    │   ├── Models/
    │   ├── Policies/
    ├── database/
    │   ├── migrations/
    │   ├── seeders/
    │   ├── factories/
    ├── routes/
    ├── tests/
    │   ├── Feature/
    │   ├── Unit/
```

---

# 🧑‍💻 Available Commands

---

## 📌 Create Module

```bash
php artisan module:create {name}
```

### Example:

```bash
php artisan module:create Inventory
```

---

## 📌 Generate Module Files

```bash
php artisan module:make
```

### Flow:

* Select existing module OR create new one
* Select artifact preset
* Generate required files

---

## 📌 Artifact Options

When running `module:make`:

```
Select artifacts to generate:

✔ API Resource
✔ Database
✔ Custom
```

---

### API Resource generates:

* Controller
* Model
* Migration
* Seeder
* Factory

---

### Database generates:

* Model
* Migration
* Seeder
* Factory

---

### Custom generates:

* Controller
* Model
* Migration
* Seeder
* Factory
* (More coming)

---

# 🧪 Example Usage

## Step 1: Create a module

```bash
php artisan module:create Inventory
```

---

## Step 2: Generate files inside module

```bash
php artisan module:make
```

Example flow:

```
Select module:
> Inventory

Select artifacts:
> API Resource

Enter name:
> ItemMaster
```

---

## Result:

```
Modules/Inventory/
├── app/Http/Controllers/ItemMasterController.php
├── app/Models/ItemMaster.php
├── database/migrations/...
├── database/seeders/...
```

---

# ⚠️ Important Notes

* Requires PSR-4 autoloading for `Modules\` namespace
* Run `composer dump-autoload` after setup
* Laravel base controller may be used inside modules

---

# Artisan Commands Summary
```
php artisan module -h
```


# 🤝 Contributing

Feel free to fork and improve the package.

---

# 📜 License

MIT License © Raza9798

---