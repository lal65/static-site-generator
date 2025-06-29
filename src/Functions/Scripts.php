<?php

namespace oe\Functions;

class Scripts {

  protected static function moveKeyBefore($arr, $find, $move) {
    if (!isset($arr[$find], $arr[$move])) {
      return $arr;
    }

    $elem = [$move=>$arr[$move]];  // cache the element to be moved
    $start = array_splice($arr, 0, array_search($find, array_keys($arr), TRUE));
    unset($start[$move]);  // only important if $move is in $start
    return $start + $elem + $arr;
  }

  public static function getScripts(): string {
    $manifests = [];
    $path = array_filter([
      'node_modules' . DIRECTORY_SEPARATOR . '@psu-online-education',
      'vendor' . DIRECTORY_SEPARATOR . 'psu-online-education' . DIRECTORY_SEPARATOR . 'static-site-generator' . DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR . '@psu-online-education',
    ], static fn($path) => file_exists($path));
    foreach (glob(current($path) . '/*/package.json') as $manifest) {
      $manifest_json = json_decode(file_get_contents($manifest), TRUE, 512, JSON_THROW_ON_ERROR);
      $manifests[$manifest_json['name']] = $manifest_json;
    }
    // Recursively sort the manifests until dependency order is met...
    while (TRUE) {
      $modified = FALSE;
      foreach ($manifests as $component => $manifest) {
        $component_position = array_search($component, array_keys($manifests), TRUE);
        if (isset($manifest['dependencies'])) {
          foreach ($manifest['dependencies'] as $dependency => $version) {
            $dependency_position = array_search($dependency, array_keys($manifests), TRUE);
            if ($component_position < $dependency_position) {
              $manifests = static::moveKeyBefore($manifests, $component, $dependency);
              $modified = TRUE;
              break 2;
            }
          }
        }
      }
      if (!$modified) {
        break;
      }
    }

    $scripts = '';
    foreach (array_keys($manifests) as $manifest) {
      $component = str_replace('@psu-online-education/', '', $manifest);
      $potential_js_file = current($path) ."/$component/dist/scripts.js";
      if (file_exists($potential_js_file)) {
        $file_content = trim(file_get_contents($potential_js_file));

        // Strip out any UTF-8 BOM sequences before inlining.
        $scripts .= preg_replace("/^\xEF\xBB\xBF/", '', $file_content);
      }
    }
    return $scripts;
  }
}