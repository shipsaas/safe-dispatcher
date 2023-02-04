# RESTFUL APIs of SafeDispatcher

SafeDispatcher ship some useful endpoints for you to:

- Listing all the failed to dispatch jobs
- View a specific one
- Retry

Note: check the [safe-dispatcher.php](../src/Configs/safe-dispatcher.php) to see available configurations for the routes.

## Listing

```
GET safe-dispatcher-apis/failed-to-dispatch-jobs
    ?limit=xx // limit per page (int, min 10 max 100)
    &page=yy // current page (int)
    &job_class= // filter by job classname (null by default)
    &failed_from= // filter by created_at, >= created_at (datetime format: Y-m-d H:i:s), null by default
    &failed_to= // filter by created_at, <= created_at (datetime format: Y-m-d H:i:s), null by default
    &wants_redispatched // will show those jobs that already redispatched (boolean) false by default
    &sort_by= // created_at, job_class (created_at by default)
    &sort_direction= // asc, desc (desc by default)
```

## View single failed to dispatch job

```
GET safe-dispatcher-apis/failed-to-dispatch-jobs/{uuid}
```

## Retry a failed to dispatch job

```
PATCH safe-dispatcher-apis/failed-to-dispatch-jobs/{uuid}
```

Note: if the job already redispatched, it won't let you redispatch again.
