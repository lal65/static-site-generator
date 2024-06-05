<?php

namespace ooe\Functions;

class Config {

  public static function getConfig(string $key): string {
    $config = yaml_parse(file_get_contents('config.yml'));
    return $config[$key];
  }
}