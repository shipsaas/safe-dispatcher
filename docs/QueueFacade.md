# Queue Facade

Queue Facade allows you to access the current/any Queue Connection directly.

SafeDispatcher is only supporting `dispatch` method, which we are usually do: `Job::dispatch` or `app(Dispatcher::class)->dispatch(...)`.

We're going to cover this soon in v1.1 or v1.2, stay tuned!

## Wrapper your push/pushRaw/pushOn/later/laterOn call

Wrap it with a try/catch and use SafeDispatcher service to insert the fail to dispatch:

```php
use SaasSafeDispatcher\Services\FailDispatcherService;

try {
    return Queue::push(new MyJob($myData));
} catch (Throwable $throwable) {
    app(FailDispatcherService::class)
        ->storeFailure($queue, $throwable, $command);

    return;
}
```
