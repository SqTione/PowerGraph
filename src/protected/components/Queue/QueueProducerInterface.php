<?php

namespace app\components\Queue;

interface QueueProducerInterface {
    public function sendMessage(string $message): void;
    public function close(): void;
}