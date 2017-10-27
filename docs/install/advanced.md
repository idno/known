# Advanced Options

Here are some configuration options which are available, but not really necessary
for most users.

## Asynchronous Event Queue

Known uses event queues to dispatch things like Webmention pings. 

By default, this dispatching is synchronous. However, you it is possible to enqueue events and have them dispatched later in an asynchronous fashion, enabling faster page loading.

### Enabling asynchronous queues

To use something the asynchronous queue, add the following line to your ```config.ini```:

```
event_queue = 'AsynchronousQueue'
```

### Running the dispatch service

Next, you need to run the Known event queue dispatching service using the Known console tool:

```
./known service-event-queue
```

!!! note "Per-domain configuration"
    If you’re using per-domain configuration you’ll need to set an environment variable in order for everything to work as expected:

    ```
    export KNOWN_DOMAIN='your.domain.name'
    ```

## Periodic Execution Service

Sometimes it is desirable to execute actions in the background and periodic intervals, the advanced periodic execution services allows you to do this.

After completing the configuration step for enabling the [Asynchronous Event Queue](#asynchronous-event-queue), you can then run the Known console periodic execution service:

```
./known.php service-cron
```

Once running, this service will periodically trigger an event to which code can listen to. Available events are ```cron/minute```, ```cron/hourly``` and ```cron/daily```.

!!! note "Service User"
    You should run both ```service-event-queue``` and ```service-cron``` as the webserver user so that it can read and write to files. On Debian this is usually ```www-data```.