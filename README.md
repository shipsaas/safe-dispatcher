# Laravel Safe Dispatcher for Queue

[![Build & Test](https://github.com/shipsaas/safe-dispatcher/actions/workflows/build.yml/badge.svg)](https://github.com/shipsaas/safe-dispatcher/actions/workflows/build.yml)

For Laravel, it has the Queue feature, all cool and easy to use, right?

But what if it fails to dispatch a job? Then you have no idea for:

- What was the data inside the msg?
- Resend the msg

Then it will cost you a lot of time to check the log, sentry issues, create retry command,... Awful, IKR?

Worries no more, SafeDispatcher got your back. Check out how it works below.

Documentation: (coming soon)

## How SafeDispatcher works?

![How does Laravel SafeDispatcher works?](./docs/SafeDispatcher.png)

## Requirements
- Laravel 9+
- PHP 8.1+

## Installation

```bash
composer require shipsaas/safe-dispatch
```

## Usage

### Dependency Injection

```php
use SaasSafeDispatcher\Bus\SafeDispatcher;

class RegisterService
{
    public function __construct(public SafeDispatcher $dispatcher) {}

    public function register(): void
    {
        $user = User::create();
        
        $job = new SendEmailToRegisteredUser($user);
        $this->dispatcher->dispatch($job);
    }
}
```

### Use Trait for your Job

```php
use SaasSafeDispatcher\Traits\SafeDispatchable;

class SendEmailToRegisteredUser implements ShouldQueue
{
    use SafeDispatchable;
}

SendEmailToRegisteredUser::safeDispatch($user);
```

## Notes

- SafeDispatcher doesn't work with batching & chaining.
  - Alternatively, you can do normal `::safeDispatch` and after finish your job, dispatch another,...
- SafeDispatcher considers the `sync` Queue as a Queue Msg.
  - Therefore, if the handling fails, Queue Msg will be stored too.

## Contribute to the project
- All changes must follow PSR-1 / PSR-12 coding conventions.
- Unit testing is a must, cover things as much as you can.

### Maintainers & Contributors
- Seth Phat

Join me 😉

## This library is useful?
Thank you, please give it a ⭐️⭐️⭐️ to support the project.

Don't forget to share with your friends & colleagues 🚀

## License
Copyright © by Seth Phat / ShipSaaS 2023 - Under MIT License.
