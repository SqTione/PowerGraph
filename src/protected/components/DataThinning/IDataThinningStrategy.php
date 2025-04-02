<?php

interface IDataThinningStrategy {
    public function thinData($data): array;
}