# Revertible model revisions for Laravel 

[![Latest Stable Version](https://poser.pugx.org/wearesweet/laravel-revisionable/v/stable?format=flat-square)](https://packagist.org/packages/wearesweet/laravel-revisionable)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/wearesweet/laravel-revisionable/run-tests?label=tests)
[![Quality Score](https://img.shields.io/scrutinizer/g/wearesweet/laravel-revisionable.svg?style=flat-square)](https://scrutinizer-ci.com/g/wearesweet/laravel-revisionable)
[![Total Downloads](https://img.shields.io/packagist/dt/wearesweet/laravel-revisionable.svg?style=flat-square)](https://packagist.org/packages/wearesweet/laravel-revisionable)

Apply the trait HasRevisions to any model to be able to: View a history of changes, revert to previous versions of the model and allow for easy moderation of changes before persisting to the database.

## About We Are Sweet

We Are Sweet is a small but mighty wb agency providing design development and consultancy services to a wide range of clients.

[Check us out](https://www.wearesweet.co.uk/)

## Installation and usage

This package requires PHP 7.6 and Laravel 6 or higher.  

Run 
```shell script
$ composer require wearesweet/laravel-revisionable
```

### HasRevisions Trait

Add the HasRevisions trait to your model(s).

```php
class User {
    use WeAreSweet\LaravelRevisionable\Traits\HasRevisions;
}
```

### Listen for certain attributes

Override revisionableAttributes so that it returns an array of attributes you want to allow revisions for. 

If using revisionableAttributes in conjunction with persistRevisions, attributes in your array will be stored as a 
revision which can later be persisted (approved) and all other attributes not in the array will be persisted imminently.

```php
class User {
    use WeAreSweet\LaravelRevisionable\Traits\HasRevisions;
    
    public function revisionableAttributes()
    {
        return 'name';
    }
}
```

# Prevent persisting model data to the database unless approved

You can tell your models not to save directly to the model and instead save the data in revisions which can be applied to the model later.

This is useful for storing draft states of your models or for moderating and approving changes. 

To do this, add the method persistRevisions to your class and have it return false.

```php
class User {
    use WeAreSweet\LaravelRevisionable\Traits\HasRevisions;
    
    public function persistRevisions()
    {
        return false;
    }
}
``` 

### View all models revision history

```php
App\User::first()->revisions;
```

### View pending model revisions

```php
App\User::first()->revisions()->pending()->get();
```

### Approve a revision

```php
App\User::first()->revisions->first()->approve();
```

### View merged pending revisions data

```php
App\User::first()->pendingRevisions();
```

#### Example input/output

##### Input:

```json
{
  "user":{
    "revisions": [
      {
        "date": "2020",
        "data": {"name": "Ben"}
      },
      {
        "date": "2010",
        "data": {"name": "Neb", "age":  23}
      }
    ]
  }
}
```

##### Output:

```json
{
  "name": "Ben",
  "age": 23
}
```

### Approve all model revisions

```php
App\User::first()->approveAllRevisions();
```

## Testing

Run the tests with:

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email developers@wearesweet.co.uk instead of using the issue tracker.

## Credits

- [Ben Watson](https://github.com/blwsh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
