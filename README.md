# laravel/hasmanywithinverse

## Why?

[Jonathan Reinink](https://github.com/reinink) wrote a great blog post about [Optimizing circular relationships in Laravel](https://reinink.ca/articles/optimizing-circular-relationships-in-laravel)

By manually setting the (`belongsTo`) relationship to a parent model on related (`hasMany`) child models, you can save unnecessary queries for the parent model -- when the child needs an instance of the parent model.

This probably sounds confusing, so just read the blog post. It's very good.

Jonathan's approach suggests using something like this:

```php
$category->products->each->setRelation('category', $category);
```

This works, but it's not very clean and there are cases when it doesn't work. For example, on model creation.

If you're accessing the parent model in `creating` and `saving` events on the children, the `->each->setRelation()` approach won't help you at all. (And if you're building a complex app with [Laravel Nova](https://nova.laravel.com), there's a high chance you're using lots of such events.)

## Practical Example & Benchmarks

I have an e-commerce application where an `Order` has child models: `OrderProduct`, `OrderStatus` and `OrderFee` (think shipping costs, payment fees, etc).

When some of those models are **being created** (`creating` Eloquent event), they are accessing the parent model.

For example, `OrderProduct`s convert their prices to `$this->order->currency`. `OrderFee`s check for other order fees, and they prevent creating themselves if a fee with the same code already exists (so that you can't have, say, the shipping cost counted twice). Etc.

This results in order creation being expensive, resulting in a large amount of n+1 queries.

### Benchmark

I haven't run a huge amount of tests, so I won't present the time differences here. I will only talk about database query count.

I have created an order with 6 products.

#### This is the amount of queries made with regular `hasMany()`

![Query count with hasMany()](https://i.imgur.com/Swl9zGw.png)

And now I just replace all of these calls:

```php
return $this->hasMany(...);
```
with these calls
```php
return $this->hasManyWithInverse(..., 'order');
```

inside the `Order` model.

#### And this is the amount of queries made with `hasManyWithInverse()`

![Query count with hasManyWithInverse()](https://i.imgur.com/TqeWIa4.png)

See the query count reduction.

The request duration was also decreased from 399ms to 301ms on my machine, though note that I did not run this test a million times to calculate an average request duration, so that benchmark might not be very accurate.

This is pretty impressive for **a free improvement that only requires changing a few simple calls to a similar method**.

But note that this is not a silver bullet for solving all n+1 queries. As you can see, even with this implemented, my app still has many duplicated queries. (Although not all are unintentional n+1s as there are a few `$this->refresh()` calls to keep the order up-to-date after state transitions).

## Installation

Laravel 6.x and 7.x is supported.

```
composer require stancl/laravel-hasmanywithinverse
```

## Usage

```php
namespace App;

use Stancl\HasManyWithInverse\HasManyWithInverse;

class Order extends Model
{
    use HasManyWithInverse;

    public function products()
    {
        // 'order' is the name of the relationship in the other model, see below
        return $this->hasManywithInverse(App\OrderProduct::class, 'order');
    }
}

class OrderProduct extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
```

You may also want to use the trait in a base Eloquent model and then use `$this->hasManyWithInverse()` without thinking about traits in the specific models.

## Details

The (simple) internals of the package are just methods copied from Eloquent source code, with a few lines added to them. The `hasManyWithInverse()` method signature is the exact same as `hasMany()` (you can set `$foreignKey` and `$localKey`), except the second argument (`$inverse`) was added to let you define the name of the relationship on the child model.
