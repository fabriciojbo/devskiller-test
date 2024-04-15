## Introduction

You are working on an online shop application. You have to finish a few functions.

The project contains tests. Your task is to make all the tests pass by writing the missing code.

### Setup

```
composer install
```

### Database seed

```
composer refresh-db
```

### Tests

```
composer test
OR
vendor/bin/phpunit
```

## Tasks

### Configure the connection to the second database

1. Add a configuration for an SQLite database located in the `database/cms_database.sqlite`. The connection has to be named `cms`.
2. Do not change the configuration of the default connection.
3. Adjust the migration file `2020_10_12_000000_create_pages_table.php` to use the `cms` connection.
4. Adjust the `Page` model to use the `cms` connection.


### Security

1. Configure the `User` model to be used by Laravel Passport.
2. Add missing `api` guard for Passport.
3. Implement the `IsAdmin` (`auth.admin`) middleware - check the `is_admin` attribute of the user.

### Products API

Implement the following endpoints:

**POST /api/products** - creates a new Product resource.

Allow only authenticated admin users (using the `api` guard and `auth.admin` middleware).

Required parameters:\
`name` - string\
`price` - integer, > 0\


Sample response (HTTP 201)
```
{
   "data":{
      "id":1,
      "name":"iPhone 10",
      "price":1000
   }
}
```

---

**POST /api/products/{id}/reviews** - creates a new ProductReview resource.

Allow only authenticated users (using the `api` guard).

Required parameters:\
`review` - int (1-10)\
`comment` - string


Sample response (HTTP 201)
```
{
   "data":{
      "id":1,
      "review":5,
      "comment":"Lorem ipsum",
      "user":{
         "id":1,
         "name":"Kody Lebsack"
      }
   }
}
```


### SMS & database notifications

1. Create a listener that will handle the `App\Events\ProductReviewed` event 
and send the `App\Notifications\ProductReviews` notification via SMS and database channels:
    - Send a notification only to admin users (`is_admin = 1`). 
    - SMS notifications use the nexmo channel that has already been installed.
2. Adjust the `App\Notifications\ProductReviewes` notifications to support SMS and DB channels:
    - SMS content - `New review for product #1`.
    - DB notification data - `['product_id' => 1]`.
    - Replace `1` with the reviewed product ID.
3. Adjust the `App\User` model to support SMS and DB channels.
    - An SMS should be sent to the phone number from the `phone_number` attribute of the `User` model.

### Database transactions

You are working on a products migration script from an external data source but there is a problem, sometimes the script does not respond.

Your job is to implement a database transaction which will rollback all changes (created products) when something goes wrong during the import (an exception is thrown).

Do not bother about the implementation of `ProductsDataSource` interface, you just need to know that it sometimes throws an exception.

TODO:
1. Make an import process in the `App\Services\ProductsImporter` transactional (single transaction wrapping the entire process). Rollback transaction on any exception.
2. Log an error with `Psr\Log\LoggerInterface::error`, pass `['offset' => $offset]` as a message context.

### Console commands

Create a console command (`products:import`) that will start the import (`App\Services\ProductsImporter`) and output information about imported products as below:

```
Imported products: 10
Name: "product name 1", price: "123"
Name: "product name 2", price: "12"
Name: "product name 3", price: "135"
...
```

### Redis page view counter

1. Implement the body of methods in the `App\Services\ViewCounter`.
2. Save the number of visits at the key named `page-views:$pageId`.
3. The `reset` method should set 0 as the value of the key.

**IMPORTANT** - the connection is mocked, so you do not need a Redis server.

### Custom error response

There is a `ProductsController` which throws the `ProductNotFound` exception.

Implement (outside of the controller) an error handler that will return a JSON response in case of an exception as below:

```
{"error":"Product with given ID does not exist"}
```

## Hints

1. The project is configured to use an SQLite database.
2. Do not modify any tests.
3. Look for comments with `@todo`.
