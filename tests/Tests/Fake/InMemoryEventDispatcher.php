<?php

namespace Tests\Fake;

use App\Domain\Event\EventDispatcherInterface;
use App\Domain\Event\EventInterface;

class InMemoryEventDispatcher implements EventDispatcherInterface
{
    public array $events = [];

    public function dispatch(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    public function getEvent(int $nb): EventInterface
    {
        return $this->events[$nb];
    }
}