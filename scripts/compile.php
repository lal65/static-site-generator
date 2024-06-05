<?php

use League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
use ooe\Functions\Config;
use ooe\Functions\Favicons;
use ooe\Functions\Styles;
use Twig\Environment;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFunction;

require_once dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$loader = new FilesystemLoader(['blocks', 'templates']);
$loader->addPath('node_modules/@psu-ooe', 'psu-ooe');
$twig = new Environment($loader);

$twig->addExtension(new MarkdownExtension());
$twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
  public function load($class) {
    if (MarkdownRuntime::class === $class) {
      return new MarkdownRuntime(new DefaultMarkdown());
    }
  }
});

$twig->addFunction(new TwigFunction('get_config', Config::class . '::getConfig'));
$twig->addFunction(new TwigFunction('get_styles', Styles::class . '::getStyles'));
$twig->addFunction(new TwigFunction('render_favicons', Favicons::class . '::renderFavicons'));

$blocks = [];
foreach (glob('blocks/*') as $region) {
  $region_name = basename($region);
  foreach (glob("$region/*.twig") as $block) {
    $block_name = basename($block);
    $blocks[$region_name][$block_name] = $twig->render($region_name . DIRECTORY_SEPARATOR . $block_name);
  }
}

$frontMatterParser = new FrontMatterParser(new LibYamlFrontMatterParser());
foreach (glob('pages/*.md') as $page) {
  $content = file_get_contents($page);
  $result = $frontMatterParser->parse($content);
  $frontmatter = $result->getFrontMatter();
  $context = [
    'blocks' => $blocks,
    'content' => $result->getContent(),
  ];

  if (is_array($frontmatter)) {
    $context += $frontmatter;
  }

  if (isset($context['page_image'])) {
    $imagick = new IMagick();
    $imagick->readImage('images/' . $context['page_image']);
    foreach ([[1920, 1280], [1280, 720], [768, 432]] as $resolution) {
      $derivative = clone $imagick;
      $derivative->scaleImage($resolution[0], $resolution[1]);
      $derivative->setImageFormat('webp');
      $derivative->writeImage('dist/images/' . $context['page_image'] . '@' . $resolution[0] . 'x' . $resolution[1] . '.webp');
    }
  }

  file_put_contents('dist' . DIRECTORY_SEPARATOR . basename($page, '.md') . '.html', $twig->render('page.twig', $context));
}
