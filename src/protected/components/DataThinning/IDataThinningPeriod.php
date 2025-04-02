<?php

interface IDataThinningPeriod {
    /**
     * Возвращает старые данные
     * @return array Старые данные
    */
    public function getOldData(): array;
}
