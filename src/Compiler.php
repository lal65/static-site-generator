<?php

namespace ooe;

use League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
use ooe\Functions\Config;
use ooe\Functions\Favicons;
use ooe\Functions\Menus;
use ooe\Functions\Scripts;
use ooe\Functions\Styles;
use Twig\Environment;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Compiler {

  protected static $compiler:
  
  static public function getInstance() {
    if (!static::$compiler) {
      $paths = array_filter([
        'blocks',
        'templates',
        'vendor' . DIRECTORY_SEPARATOR . 'psu-ooe' . DIRECTORY_SEPARATOR . 'static-site-generator' . DIRECTORY_SEPARATOR . 'blocks',
        'vendor' . DIRECTORY_SEPARATOR . 'psu-ooe' . DIRECTORY_SEPARATOR . 'static-site-generator' . DIRECTORY_SEPARATOR . 'templates',
      ], static fn($path) => file_exists($path));

      $loader = new FilesystemLoader($paths);
      $paths = array_filter([
        'node_modules/@psu-ooe',
        'vendor' . DIRECTORY_SEPARATOR . 'psu-ooe' . DIRECTORY_SEPARATOR . 'static-site-generator' . DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR . '@psu-ooe',
      ], static fn($path) => file_exists($path));
      
      foreach ($paths as $path) {
        if (file_exists($path)) {
          $loader->addPath($path, 'psu-ooe');
          break;
        }
      }

      $twig = new Environment($loader);

      $twig->addExtension(new MarkdownExtension());
      $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
        public function load($class) {
          if (MarkdownRuntime::class === $class) {
            return new MarkdownRuntime(new DefaultMarkdown());
          }
        }
      });

      $twig->addFilter(new TwigFilter('clean_unique_id', static function($id) {
        $id = str_replace(' ', '-', $id);
        return $id . '-' . uniqid();
      }));

      $twig->addFunction(new TwigFunction('get_config', Config::class . '::getConfig'));
      $twig->addFunction(new TwigFunction('get_styles', Styles::class . '::getStyles'));
      $twig->addFunction(new TwigFunction('get_scripts', Scripts::class . '::getScripts'));
      $twig->addFunction(new TwigFunction('render_favicons', Favicons::class . '::renderFavicons'));
      $twig->addFunction(new TwigFunction('get_menu_items', Menus::class . '::getMenuItems'));

      static::$compiler = $twig;
    }
    return static::$compiler;
  }
}
