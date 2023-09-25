<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## REST API for CRUD operations on product resources

This is a simple service for CRUD operations on product resources via appropriate endpoints. It is used Laravel's functionality and present solution in the core of the framework to acchive the required functionality.

The routes are defined in the `api.php` file: for authentication and for CRUD operations. Since Sanctum package comes in a fresh Laravel installation, it is used for authentication.

For different CRUD operations `apiResource` static method is used that wraps up all HTTP methods for CRUD operations: GET, POST, PUT|PATCH AND DELETE. From the task it's not clear (at least to mee) whether routes for fetching all products or single product should be public or protected. This implementation protects the routes but if the request is to some of the mentioned routes shoud be public, they should be moved out from the protected group. `missing` method is used to handle the case when there is no resource that is required to be used for some of the actions.

For route protection are used 2 middlewares: one is for rate limit and the other one for authentication. Product routes have the name prefix `api.` for naming routes, how their names would be different from the names of web routes. API version is marked as `v1` as API versioning rules.

`ProductController` is the API resource controller for manipulating with product resources via appropriate methods: index, show, create, update and delete. Those methods are specified for API resource controllers.

`ProductResource` is used to easily adopt/transform response from the API depending on the request from the other side (front-end or 3rd party services).

All requests for resource creating/mutating are validated against predefined rules. For that purpose, it is used `ProductCreateRequest` and `ProductUpdateRequest`, `RegisterRequest` and `LoginRequest`.

Authorization for some actions is provided by using `ProductPolicy`. In that case only user that owns the resource (creator of the resource) is allowed to POST, PUT|PATCH or DELETE the resource.

## Requirements

For this application to work appropriately, the running environment must have the next tools installed:
- Git V2.X
- PHP V8.2.X
- Composer V2.5.1

## Installation

1. clone repository
2. install dependencies via Composer: `composer install`
3. make a copy of `.env.example` to `.env`
4. generate application key: `php artisan key:generate`
5. in `.env` insert information about DB - connection, database name, username and password. Or if you want to use `sqlite`, without having some of the robust DBs, just do the next thing:
    ```php
    DB_CONNECTION=sqlite
    # DB_HOST=127.0.0.1
    # DB_PORT=3306
    # DB_DATABASE=laravel
    # DB_USERNAME=root
    # DB_PASSWORD=
    ```

## Testing

1. PHPUnit testing is set up for in-memory testing with SQLite. If you want to run tests over some of the other databases, just comment on the following lines in `phpunit.xml`:
    ```xml
    <!-- <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/> -->
    ```
    and setup the database's connection and credentials in `.env` file.
    Feature test `ProductControllerTest` fully covers the `ProductController` class. So, before all it is good to run those tests to see the results.

2. Testing with Postman
    There are `json` files in `/docs/postman-collections/` for the Postman's collection and testing environment.
    - Import them into Postman.
    - If you are using SQLite database, create a new file in `/database/database.sqlite`. If you are using, let's say, `MySQL` setup the database credentials in `.env` file.
    - run `php artisan migrate` to populate the DB with the tables
    - run `php artisan serve` to have up and running web server

    Since the routes are protected, the first thing is user's registration or login, and after that all CRUD actions could be tested.

