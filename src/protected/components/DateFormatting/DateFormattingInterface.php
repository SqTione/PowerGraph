<?php

namespace app\components\DateFormatting;

interface DateFormattingInterface {
    public function formatDate(string $periodType, string $periodValue): string;
}