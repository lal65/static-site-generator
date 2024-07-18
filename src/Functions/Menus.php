<?php

namespace ooe\Functions;

use League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

class Menus {

  public static function getMenuItemsRecursive($directory): array {
    $items = [];
    $base_path = Config::getConfig('base_path');
    $frontMatterParser = new FrontMatterParser(new LibYamlFrontMatterParser());

    foreach (new \DirectoryIterator($directory) as $file) {
      /** @var \SplFileInfo $file */
      if ($file->isDot()) {
        continue;
      }
      if ($file->isFile()) {

        $frontmatter = $frontMatterParser->parse(file_get_contents($file->getPathname()))->getFrontMatter();
        if (isset ($frontmatter['menu_link_title'])) {
          $path = preg_replace('/^pages\//', '', $file->getPathname());
          $path = preg_replace('/\.md$/', '', $path);
          $items[$path]['title'] = $frontmatter['menu_link_title'];
          $items[$path]['description'] = $frontmatter['meta']['description'];
          $items[$path]['url'] = $path === 'index' ? "/$base_path" : "/$base_path/$path";
          $items[$path]['sort_order'] = $frontmatter['sort_order'] ?? 0;
        }
      }
      if ($file->isDir()) {
        $path = preg_replace('/^pages\//', '', $file->getPathname());
        $items[$path]['below'] = Menus::getMenuItemsRecursive($file->getPathname());
      }
    }
    uasort($items, static fn ($lhs, $rhs) => $lhs['sort_order'] - $rhs['sort_order']);
    return $items;
  }

  public static function getMenuItems(): array {
    return static::getMenuItemsRecursive('pages');
  }
}