<?php

namespace ooe;

use ooe\Filters\CleanUniqueId;
use ooe\Functions\Breadcrumbs;
use ooe\Functions\Config;
use ooe\Functions\Example;
use ooe\Functions\Favicons;
use ooe\Functions\Menus;
use ooe\Functions\Scripts;
use ooe\Functions\Styles;
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