<?php

interface QueueProducerInterface {
    public function sendMessage(string $message): void;
    public function close(): void;
}