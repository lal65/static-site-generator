<?php

namespace ooe;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\Table\TableExtension;
use ooe\Functions\Config;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Markdown\LeagueMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Webuni\FrontMatter\Twig\FrontMatterLoader;
use Webuni\FrontMatter\Twig\TwigCommentFrontMatter;

class Compiler {

  protected static ?Environment $compiler = NULL;

  /**
   * Gets a twig compiler instance.
   *
   * @return Environment
   *   A twig compiler instance.
   * @throws \Twig\Error\LoaderError
   */
  public static function getInstance() {
    if (!static::$compiler) {
      $debug = Config::getConfig('debug', FALSE);
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

      $twig = new Environment($loader, ['debug' => $debug]);
      if ($debug) {
        $twig->addExtension(new DebugExtension());
      }
      $twig->addExtension(new Extension());
      $twig->addExtension(new MarkdownExtension());

      $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {

        /**
         * {@inheritdoc}
         */
        public function load($class): ?MarkdownRuntime
        {
          $runtime = NULL;
          if (MarkdownRuntime::class === $class) {
            $converter = new CommonMarkConverter(['html_input' => 'allow']);
            $converter->getEnvironment()->addExtension(new TableExtension());
            $runtime = new MarkdownRuntime(new LeagueMarkdown($converter));
          }
          return $runtime;
        }
      });

      static::$compiler = $twig;
    }
    return static::$compiler;
  }
}
