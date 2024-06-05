<?php

namespace ooe\Functions;

use Genkgo\Favicon\FullPackageGenerator;
use Genkgo\Favicon\Input;
use Genkgo\Favicon\InputImageType;
use Genkgo\Favicon\WebApplicationManifest;
use Genkgo\Favicon\WebApplicationManifestDisplay;

class Favicons {

  public static function renderFavicons(): string {
    $config = yaml_parse(file_get_contents('config.yml'));
    $generator = FullPackageGenerator::newGenerator();
    $manifest = new WebApplicationManifest(
      WebApplicationManifestDisplay::Standalone,
      $config['site_name'],
      $config['site_description'],
      $config['theme_color'],
      $config['tile_color']
    );

    $source = Input::fromFile('favicon.svg', InputImageType::SVG);
    foreach ($generator->package($source, $manifest, '/') as $filename => $contents) {
      file_put_contents('dist' . DIRECTORY_SEPARATOR . $filename, $contents);
    }
    $document = new \DOMDocument('1.0', 'UTF-8');
    foreach ($generator->headTags($document, $manifest, '/') as $tag) {
      $tags[] = $document->saveHTML($tag);
    }
    return implode(PHP_EOL, $tags);
  }
}