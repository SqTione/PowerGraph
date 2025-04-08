<?php

interface DateFormattingInterface {
    public function formatDate(string $periodType, string $periodValue): string;
}