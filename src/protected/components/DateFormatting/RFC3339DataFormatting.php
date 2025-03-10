<?php

namespace app\components\DateFormatting;
use DateTime;
use Exception;
use InvalidArgumentException;

class RFC3339DataFormatting implements DateFormattingInterface {

    /**
     * Форматирование даты в cоответствии с RFC 3339
    */
    public function formatDate(string $periodType, string $periodValue): string {
        try {
            switch ($periodType) {
                case 'moment':
                    $date = new DateTime($periodValue);
                    return $date->format('Y-m-d\TH:i:sP');

                case 'hour':
                    $date = new DateTime($periodValue);
                    return $date->setTime($date->format('H'), 0, 0)->format('Y-m-d\TH:i:sP');

                case 'day':
                    $date = new DateTime($periodValue);
                    return $date->format('Y-m-d');

                case 'month':
                    $date = new DateTime($periodValue);
                    return $date->format('Y-m');

                case 'year':
                    $date = intval($periodValue);
                    if ($date <= 0) {
                        throw new InvalidArgumentException("Invalid year value. Must be a positive integer.");
                    }
                    return $date;

                default:
                    throw new InvalidArgumentException("Invalid period value format for period type '$periodType'.");
            }
        } catch (Exception $e) {
            throw new InvalidArgumentException("Invalid period value format for period type '$periodType'. Error: " . $e->getMessage());
        }
    }
}