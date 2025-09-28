# Court - A Plain PHP Validation Library

Court is an extremely simple and lightweight validation library for PHP. It provides a featherweight framework to validate any kind of data in a structured and reusable way. It is dependency-free and works out of the box with Laravel.

You can imagine Court as a set of building blocks that you can use to create your own validation logic. It doesn't impose any specific structure or pattern on your code, allowing you to use it in a way that best fits your needs.

This package works out of the box, though it is also highly extensible. You can create your own objection types, guard resolvers, objection collectors and the objection bag returned by the verdict. We encourage you to extend the package to fit your specific use cases.

## Version Table

| Court | PHP  | Laravel |
|-------|------|---------|
| 1.x   | ^8.2 | ^11.0   |

## Installation

```bash
composer require bumpcore/court
```

## Quick Start

```php
use BumpCore\Court\Court;

$subject = "I love elephants and bears";

$verdict = Court::of($subject)
    ->guards([
        function ($subject, $objection) {
            if (str_contains($subject, 'elephants')) {
                $objection('No elephants allowed!');
            }
        },
        function ($subject, $objection) {
            if (str_contains($subject, 'bears')) {
                $objection('No bears allowed!');
            }
        },
    ])
    ->verdict();

if ($verdict->isNotEmpty()) {
    foreach ($verdict->all() as $objection) {
        echo $objection->value() . PHP_EOL;
    }
}
```

## Philosophy

Court is not built for replacing existing validation solutions like Laravel's or Symfony's validation components. Instead, it's meant to be used alongside them, providing a simple way to enforce business rules and complex validation logic that doesn't fit neatly into traditional validation paradigms. Nonetheless, the extendible nature of Court allows you to build your own validation framework on top of it if you wish.

## Core Concepts

### Subject

The subject is the data that you want to validate. It can be of any type: string, integer, array, object, etc.

### Court

The main orchestrator that runs guards against the subject and collects objections.

### Guards

Functions or classes that examine the subject and raise objections if validation rules are violated. Guards can be:

- Closures / callables
- Invokable classes
- Classes with `verdict()` method
- Classes with `handle()` method

### Objections

Issues raised by guards when the subject fails validation. Each objection contains a value describing the problem. Though, virtually they can hold any type of data.

### Verdict

The final result of the validation process, containing all objections raised by the guards.

## Basic Usage

### Creating a Court

```php
$court = Court::of($subject);
// or
$court = new Court($subject);
```

### Adding Guards

#### Array of Guards

```php
$court->guards([
    $guard1,
    $guard2,
]);
```

#### Variadic Arguments

```php
$court->guards($guard1, $guard2);
```

### Getting the Verdict

```php
$verdict = $court->verdict();

if ($verdict->isNotEmpty()) {
    foreach ($verdict->all() as $objection) {
        echo $objection->value() . PHP_EOL;
    }
}
```

## Guards

By default, Court supports various types of guards. You can use any of the following:

### Closure / Callable Guards

```php
$guard = function ($subject, $objection) {
    if ($subject < 18) {
        $objection('Subject must be at least 18 years old.');
    }
};
```

### Invokable Class Guards

```php
class AgeGuard {
    public function __invoke($subject, $objection) {
        if ($subject < 18) {
            $objection('Subject must be at least 18 years old.');
        }
    }
}

$guard = new AgeGuard();
```

### Class with `verdict()` Method

```php
class AgeGuard {
    public function verdict($subject, $objection) {
        if ($subject < 18) {
            $objection('Subject must be at least 18 years old.');
        }
    }
}

$guard = new AgeGuard();
```

### Class with `handle()` Method

```php
class AgeGuard {
    public function handle($subject, $objection) {
        if ($subject < 18) {
            $objection('Subject must be at least 18 years old.');
        }
    }
}

$guard = new AgeGuard();
```

## Working with Verdicts

The `verdict()` method returns an `ObjectionBag` instance containing all objections raised by the guards. You can interact with the verdict in several ways:

```php
$verdict = $court->verdict();

// Get all objections
$objections = $verdict->all();

// Check if there are any objections
$hasIssues = $verdict->isNotEmpty();
$hasNoIssues = $verdict->isEmpty();

// Merge with another verdict
$verdict->merge($anotherCourt->verdict());
```

## Laravel Integration

Court has out-of-the-box integration with Laravel. Everything works as expected, but while providing guards, you can leverage Laravel's service container to resolve dependencies.

You can use dependency injection in your guards:

```php
use BumpCore\Court\Court;

$subject = "I love elephants and bears";

$verdict = Court::of($subject)
    ->guards([
        \App\Guards\ElephantGuard::class,
        \App\Guards\BearGuard::class,
    ])
    ->verdict();
```

```php
// app/Guards/ElephantGuard.php
namespace App\Guards;

use App\Services\ElephantService;

class ElephantGuard {
    public function __construct(protected ElephantService $elephantService) {}

    public function __invoke($subject, $objection) {
        if ($this->elephantService->hasElephants($subject)) {
            $objection('No elephants allowed!');
        }
    }
}
```

## Extending Court

As we mentioned earlier, Court is highly extensible and it is encouraged to extend the package to fit your specific use cases.

For instance, the default `Objection` implementation holds any value. Instead, you can create your own objection type to hold a translatable message.

In the following we are using Laravel's `PotentiallyTranslatedString` class as `Objection`.

```php
use Illuminate\Translation\PotentiallyTranslatedString;
use BumpCore\Court\Contracts\Objection as ObjectionContract;

class PotentiallyTranslatedObjection extends PotentiallyTranslatedString implements ObjectionContract {
    // You can add custom methods or properties if needed
}
```

Then, you can instruct Court to use your custom objection type by providing a custom objection factory.

```php
use BumpCore\Court\Court;
use BumpCore\Court\Factory;

$verdict = Court::of($subject)
    ->setObjectionFactory(
        fn($value) => new PotentiallyTranslatedObjection($value)
    )
    ->guards([
        fn($subject, $objection) => $objection('Translate this!')->translate()
    ])
    ->verdict();

// or if you want to set it globally

Factory::setObjectionFactory(
    fn($value) => new PotentiallyTranslatedObjection($value)
);
```

Isn't this neat? The true power of Court lies in its extensibility. Besides objections, you can also create your own guard resolvers, objection collectors and objection bags.

### Custom Objection Collector

By default, Court uses a simple objection collector that collects objections in an array. You can create your own objection collector by implementing the `ObjectionCollector` interface.

```php
use BumpCore\Court\Contracts\ObjectionCollector as ObjectionCollectorContract;

class CustomObjectionCollector implements ObjectionCollectorContract {
    protected array $objections = [];

    public function all(): array {
        return $this->objections;
    }

    public function closure() {
        return function ($value) {
            $this->objections[] = $value;
        };
    }
}

// Then, you can instruct Court to use your custom objection collector

Court::of($subject)
    ->setObjectionCollectorFactory(fn() => new CustomObjectionCollector())
    ->guards([
        fn($subject, $objection) => $objection('Custom objection collector!')
    ])
    ->verdict();

// or if you want to set it globally

Factory::setObjectionCollectorFactory(fn() => new CustomObjectionCollector());
```

> **Note**: Since the default objection collector is also responsible for creating objections, when a custom collector is provided, the default objection factory will be ignored. So, if you want to use a custom objection type, you need to create the objection inside your custom collector.

### Custom Guard Resolver

The default guard resolver can resolve guards of various types (closures, invokable classes, classes with `verdict()` or `handle()` methods). You can provide your own guard resolver by simply providing a callable that takes a guard and returns a callable with the signature `callable($subject, $objection)`.

```php
$resolver = function ($guard) {
    if ($guard === 'required') {
        return function($subject, $objection) {
            if (empty($subject)) {
                $objection('This field is required.');
            }
        };
    }
    elseif ($guard === 'email') {
        return function($subject, $objection) {
            if (!filter_var($subject, FILTER_VALIDATE_EMAIL)) {
                $objection('This field must be a valid email address.');
            }
        };
    }

    if (is_callable($guard)) {
        return $guard;
    }
    
    throw new \InvalidArgumentException('Unsupported guard type');
};

Court::of('john.doe@example.com')
    ->setGuardResolver($resolver)
    ->guards([
        'required',
        'email',
        fn($subject, $objection) => User::where('email', $subject)->exists() 
            && $objection('Email is already taken.')
    ])
    ->verdict();

// or if you want to set it globally

Factory::setGuardResolver($resolver);
```

### Custom Objection Bag

It is also possible to create your own objection bags by setting a custom objection bag factory. The objection bag must implement the `ObjectionBag` interface.

```php
use BumpCore\Court\Contracts\ObjectionBag as ObjectionBagContract;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Support\MessageBag;

class CustomObjectionBag implements ObjectionBagContract, MessageProvider {
    protected array $objections = [];

    public function __construct(array $objections = []) {
        $this->objections = $objections;
    }

    public function all(): array {
        return $this->objections;
    }

    public function isEmpty(): bool {
        return empty($this->objections);
    }

    public function isNotEmpty(): bool {
        return !$this->isEmpty();
    }

    public function merge($objections) {
        if ($objections instanceof ObjectionBagContract) {
            $objections = $objections->all();
        }
        
        $this->objections = array_merge($this->objections, $objections);
        
        return $this;
    }

    public function messages() {
        return new MessageBag(array_map(fn($objection) => (string) $objection->value(), $this->objections));
    }
}

// Then, you can instruct Court to use your custom objection bag

$verdict = Court::of($subject)
    ->setObjectionBagFactory(fn(array $objections) => new CustomObjectionBag($objections))
    ->guards([
        fn($subject, $objection) => $objection('Custom objection bag!')
    ])
    ->verdict();

echo $verdict->messages()->first();

// or if you want to set it globally
Factory::setObjectionBagFactory(fn(array $objections) => new CustomObjectionBag($objections));
```

## Real-World Examples

The aim of this package is providing a simple & structured "validation pipeline". The subject doesn't necessarily need to be user input. It can be any kind of data that needs to be validated. You may find the following examples useful.

### Validating Customer Deletion

```php
use BumpCore\Court\Court;

class DoesCustomerHavePendingOrders {
    public function __invoke($customer, $objection) {
        if ($customer->orders()->where('status', 'pending')->exists()) {
            $objection('Customer has pending orders.');
        }
    }
}

class DoesHaveChildren {
    public function __invoke($customer, $objection) {
        if ($customer->children()->exists()) {
            $objection('Customer has children.');
        }
    }
}

$customer = Customer::find(1);

$verdict = Court::of($customer)
    ->guards([
        new DoesCustomerHavePendingOrders(),
        new DoesHaveChildren(),
    ])
    ->verdict();

if ($verdict->isNotEmpty()) {
    throw new DeleteFailedException($verdict);
} else {
    $customer->delete();
}
```

### A Wrapper to Unify Deletions

```php
use BumpCore\Court\Court;

abstract class Destroyer {
    public function guards() {
        return [];
    }

    public function destroyOrFail($subject) {
        $verdict = Court::of($subject)
            ->guards($this->guards())
            ->verdict();

        if ($verdict->isNotEmpty()) {
            throw new DeleteFailedException($verdict);
        }

        $this->destroy($subject);
    }

    abstract protected function destroy($subject);
}

class CustomerDestroyer extends Destroyer {
    public function guards() {
        return [
            new DoesCustomerHavePendingOrders(),
            RelationGuard::for('children', 'Customer has children.')
                ->and('payments', 'Customer has payments.'),
        ];
    }

    protected function destroy($customer) {
        $customer->delete();
    }
}
```

### Product & Product Stock Validation Before Placing an Order

```php
use BumpCore\Court\Court;

class IsProductActive {
    public function __invoke($product, $objection) {
        if (!$product->is_active) {
            $objection("Product {$product->name} is not active.");
        }
    }
}

class HasSufficientStock {
    public function __invoke($product, $objection) {
        if ($product->stock <= 0) {
            $objection("Product {$product->name} is out of stock.");
        }
    }
}

$verdict = Court::of($product)
    ->guards([
        new IsProductActive(),
        new HasSufficientStock(),
    ])
    ->verdict();

if ($verdict->isNotEmpty()) {
    throw new OutOfStockException($verdict);
}

// Proceed with order placement...
```

## Testing

Run the test suite:

```bash
composer test
```

Run with coverage:

```bash
composer test:coverage
```

# Contributing

Contributions are welcome! If you find a bug or have a suggestion for improvement, please open an issue or create a pull request. Below are some guidelines to follow:

* Fork the repository and clone it to your local machine.
* Create a new branch for your contribution.
* Make your changes and test them thoroughly.
* Ensure that your code adheres to the existing coding style and conventions.
* Commit your changes and push them to your forked repository.
* Submit a pull request to the main repository.

Please provide a detailed description of your changes and the problem they solve. Your contribution will be reviewed, and feedback may be provided. Thank you for your help in making this project better!


### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`composer test`)
5. Check code quality (`composer phpstan`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Development Setup

```bash
git clone https://github.com/bumpcore/court.git
cd court
composer install
composer test
```

### Code Style

- Follow PSR-12 coding standards
- Add type hints where possible
- Write comprehensive tests
- Update documentation for new features

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

# Credits

- [Abdulkadir Cemiloğlu](https://github.com/megastive19)
- [All Contributors](../../contributors)

# License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
