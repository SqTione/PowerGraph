<?php

class EnvHelper {
  /**
   * Получает и возвращает значения из файла .env
   * @param string $key Название переменной из .env
   * @param mixed|null $default Стандартное значение переменной
   * @return mixed|null Значение переменной из .env
  */
  public static function getEnv(string $key, $default = null) {
    return getenv($key) ?: $default;
  }
}
