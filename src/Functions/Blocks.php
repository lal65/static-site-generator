<?php

namespace oe\Functions;

use Twig\Environment;

class Blocks {
  public static function getBlocks(Environment $twig, array $context): array {
    $blocks = [];
    foreach (glob('blocks/*') as $region) {
      $region_name = basename($region);
      $blocks[$region_name] = [];
      foreach (glob("$region/*.twig") as $block) {
        $block_config = [
          'sort_order' => 0,
        ];
        $block_name = basename($block);
        $block_obj = $twig->load($region_name . DIRECTORY_SEPARATOR . $block_name);
        $block_src = file_get_contents($block_obj->getSourceContext()->getPath());
        if (preg_match('/^{#---(.+)---#}/s', $block_src, $match) === 1) {
          $block_config = array_replace_recursive($block_config, yaml_parse($match[1]));
        }
        $block_config['content'] = $twig->render($region_name . DIRECTORY_SEPARATOR . basename($block), $context);
        $blocks[$region_name][$block_name] = $block_config;
      }
      uasort($blocks[$region_name], static fn($lhs, $rhs) => $lhs['sort_order'] - $rhs['sort_order']);
      $blocks[$region_name] = array_map(static fn($block_config) => $block_config['content'], $blocks[$region_name]);
    }
    return $blocks;
  }
}