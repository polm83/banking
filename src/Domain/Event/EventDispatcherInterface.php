<?php

namespace App\Domain\Event;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void;
}