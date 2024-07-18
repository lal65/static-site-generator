<?php

namespace ooe\Filters;

class CleanUniqueId {

  public static function cleanUniqueId(string $id): string {
    return str_replace([' ', '.'], '-', uniqid($id, true));
  }
}