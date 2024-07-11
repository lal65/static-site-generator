<?php

namespace ooe\Filters;

class CleanUniqueId {

  public static function cleanUniqueId(string $id): string {
    $id = str_replace(' ', '-', $id);
    return uniqid($id, true);
  }
}