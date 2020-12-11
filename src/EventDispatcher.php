<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Psr\EventDispatcher\EventDispatcherInterface;
use Prozorov\DataVerification\Events\AbstractEvent;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * {@inheritDoc}
     */
    public function dispatch(object $event)
    {
        if (! ($event instanceof AbstractEvent)) {
            throw new \InvalidArgumentException('Data-locker event dispatcher can only work with its own events');
        }

        event($event);
        event($event->getName(), $event);
    }
}
