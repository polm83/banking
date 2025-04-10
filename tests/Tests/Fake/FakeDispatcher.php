<?php

namespace Tests\Fake;

use App\Domain\Event\EventDispatcherInterface;

class FakeDispatcher implements EventDispatcherInterface
{
    public array $dispatched = [];

    public function dispatch(object $event): void
    {
        $this->dispatched[] = $event;
    }
}