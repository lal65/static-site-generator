<?php

namespace ooe\Functions;

use League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

class Breadcrumbs {
  public static function getBreadcrumbs(string $path): array {
    $crumbs = [['url' => '/', 'text' => 'Home']];

    $frontMatterParser = new FrontMatterParser(new LibYamlFrontMatterParser());
    $root = '';
    foreach (explode('/', $path) as $part) {
      $part_without_extension = preg_replace('/\.md$/', '', $part);
      $metadata = $frontMatterParser->parse(file_get_contents('pages/' . $root . $part_without_extension . '.md'))->getFrontMatter();

      if (!str_ends_with($part, '.md')) {
        $crumbs[] = ['url' => $root . '/' . $part_without_extension, 'text' => $metadata['page_title']];
      }
      else {
        $crumbs[] = ['text' => $metadata['page_title']];
      }
      $root .= $part_without_extension . '/';
    }
    return $crumbs;
  }
}