<?php

namespace App\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(string $stream, string $subject, string $event, array $payload): void;
}
