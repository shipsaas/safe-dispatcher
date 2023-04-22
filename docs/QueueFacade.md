# Queue Facade

Queue Facade allows you to interact with the current/any Queue Connection directly.

If you prefer using Queue Facade, please use our `SafeQueue` wrapper.

Available on **v1.2.0**.

## Usage

```php
use SaasSafeDispatcher\Services\SafeQueue;

# Use "default" queue connection
SafeQueue::prepareFor(new Job())
    ->push(); # Push to default queue name
    
SafeQueue::prepareFor(new Job())
    ->push('high'); # Push to "high" queue name

# Use "redis" queue connection
SafeQueue::prepareFor(new Job(), 'redis')
    ->later(\Carbon\Carbon::now()->addMinutes(10)); # Push to "default" queue name

SafeQueue::prepareFor(new Job(), 'redis')
    ->later(\Carbon\Carbon::now()->addMinutes(10), 'low'); # Push to "low" queue name
```

## Notes

### Single Push
`SafeQueue` only supports single push. So if you have a list of queued jobs, loop them 😆

```php
collect($jobs)->each(fn ($job) => SafeQueue::prepareFor($job)->push());
```

### Strict Contract

`SafeQueue` expects your job class must implement the `ShouldQueue`, please do so.

Also, you can obviously add the connection, queue & delay from your job class as well 😉

### Love your life

Remember to use `SafeQueue` over `Queue` facade 😉
