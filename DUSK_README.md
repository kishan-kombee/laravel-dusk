# Laravel Dusk User Registration Tests

This project includes comprehensive browser tests for user registration using Laravel Dusk.

## Setup

1. Make sure you have Chrome installed on your system
2. Install dependencies: `composer install`
3. Set up your environment: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`

## Running the Tests

### Run All Dusk Tests

```bash
php artisan dusk
```

### Run Specific Test Class

```bash
php artisan dusk tests/Browser/UserRegistrationTest.php
```

### Run Specific Test Method

```bash
php artisan dusk --filter testUserCanRegisterSuccessfully
```

### Run Tests with Screenshots

```bash
php artisan dusk --screenshot
```

## Test Coverage

The registration tests cover:

1. **Successful Registration** - Valid user data creates a new user
2. **Invalid Email** - Rejects malformed email addresses
3. **Password Mismatch** - Rejects when passwords don't match
4. **Duplicate Email** - Prevents registration with existing email
5. **Missing Fields** - Validates required fields
6. **Short Password** - Enforces minimum password length

## Test Files

-   `tests/Browser/UserRegistrationTest.php` - Main registration test suite
-   `tests/Browser/ExampleTest.php` - Basic example tests including registration page access
-   `tests/Browser/Pages/RegisterPage.php` - Page Object for registration form

## Features

-   **Page Objects** - Clean, maintainable test structure
-   **Database Testing** - Verifies data persistence
-   **Form Validation** - Tests all validation rules
-   **Error Handling** - Ensures proper error messages
-   **Responsive Design** - Tests work on different screen sizes

## Troubleshooting

If tests fail:

1. Ensure Chrome is installed and accessible
2. Check that the application is running: `php artisan serve`
3. Verify database connection and migrations
4. Check for any JavaScript errors in the browser console
