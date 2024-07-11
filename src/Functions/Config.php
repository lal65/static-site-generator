<?php

namespace ooe\Functions;

class Config {

  public static function getConfig(string $key, mixed $default_value = NULL): mixed {
    $config = yaml_parse(file_get_contents('config.yml'));
    return $config[$key] ?? $default_value;
  }
}