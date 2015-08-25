# Laravel Doorkeeper

## Intro

This package counts the amount of records on a relationship and compares them with a given set of limits. This can be useful to determine if a user has reached the limit of files he can upload or something similar.

## Installation

```js
composer require tshafer/laravel-doorkeeper:1.0.*@dev
```

## Example

#### Model
```php
<?php
namespace App;

use Tshafer\Doorkeeper\Traits\Doorkeeper;
use Tshafer\Doorkeeper\Contracts\DoorkeeperContract;

class User extends Model implements DoorkeeperContract
{
    use Doorkeeper;

    /**
     * Default limits if no specific ones are given.
     *
     * @type array
     */
    public $limits = [
        'posts' => 5,
        'files' => 10,
    ];

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function files() {
        return $this->hasMany('App\File');
    }
}
```

#### Middleware
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ReachedLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::check();

        if ( $user->limits($user->subscription->limits)->fails() ) {
            return redirect()->route('billing');
        }

        return $next($request);
    }
}
```

#### Controller
```php
<?php
namespace App\Http\Controllers;

class DashboardController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('reachedLimits');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view('dashboard');
    }
}
```

## Methods

#### Perform checks with custom limits.
```php
$user->limits($user->subscription->limits)->passes();
$user->limits($user->subscription->limits)->fails();
```

#### Perform a check and see if it passes.
```php
$user->passes();
```

#### Perform a check and see if it fails.
```php
$user->fails();
```

#### Get all limits.
```php
$user->allowed();
```

#### Get a specific limit.
```php
$user->allowed('posts');
```

#### Get the current counter.
```php
$user->current();
```

#### Get a specific counter.
```php
$user->current('posts');
```

#### Check if the overall limit has been reached.
```php
$user->maxed();
```

#### Check if a specific limit has been reached.
```php
$user->maxed('posts');
```

## To-Do
- Refactor the `DoorkeeperListener->compare` method to use `Illuminate\Support\Collection` methods.
