# ASSIGNMENT - IMP

## Requirement

- php 8.0
- postgresql/mysql

## Install

``` php
composer install
```

``` text
cp .env.example .env
```

## Configuration

```php
php artisan key:generate
```

```php
php artisan jwt:secret
```

```php
php artisan migrate --seed
```

## Run Project

Command Terminal

```php
php artisan serve
```

Laragon / Valet

```php
{folder-project}.test
```

## Account

| username | password |
|-|-|
| test_user | password |
