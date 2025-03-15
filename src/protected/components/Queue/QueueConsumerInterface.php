<?php

interface QueueConsumerInterface {
    public function consume(callable $callback): void;
    public function wait(): void;
    public function close(): void;
}