<?php

namespace oe;

use oe\Filters\CleanUniqueId;
use oe\Functions\Breadcrumbs;
use oe\Functions\Config;
use oe\Functions\Example;
use oe\Functions\Favicons;
use oe\Functions\Menus;
use oe\Functions\Scripts;
use oe\Functions\Styles;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return parent::getFunctions() + [
        new TwigFunction('get_config', Config::class . '::getConfig'),
        new TwigFunction('get_styles', Styles::class . '::getStyles'),
        new TwigFunction('get_scripts', Scripts::class . '::getScripts'),
        new TwigFunction('render_favicons', Favicons::class . '::renderFavicons'),
        new TwigFunction('get_menu_items', Menus::class . '::getMenuItems'),
        new TwigFunction('get_breadcrumbs', Breadcrumbs::class . '::getBreadcrumbs'),
        new TwigFunction('example', Example::class . '::example'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return parent::getFilters() + [
      new TwigFilter('clean_unique_id', CleanUniqueId::class . '::cleanUniqueId'),
    ];
  }
}