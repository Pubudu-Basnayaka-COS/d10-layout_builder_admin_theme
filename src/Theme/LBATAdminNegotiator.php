<?php

namespace Drupal\layout_builder_admin_theme\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Forces the admin theme when using layout builder.
 */
class LBATAdminNegotiator implements ThemeNegotiatorInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Layout Builder Admin Theme - Admin Negotiator constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    // Check if enabled in config.
    $is_enabled = $this->configFactory->get('layout_builder_admin_theme.config')->get('lbat_enable_admin_theme');
    if (!$is_enabled) {
      return FALSE;
    }

    // Get and check the route.
    $route = $route_match->getRouteObject();
    if (empty($route)) {
      return FALSE;
    }

    // Get and check the form.
    $form = $route->getDefault('_entity_form');
    if (empty($form)) {
      return FALSE;
    }

    // If the form is one of these - apply the admin theme.
    $layout_forms = [
      // Standard Layout builder entity form.
      'entity_view_display.layout_builder',
      // Layout builder library form.
      // @see https://www.drupal.org/project/layout_library.
      'layout.layout_builder',
    ];

    if (in_array($form, $layout_forms)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return $this->configFactory->get('system.theme')->get('admin');
  }

}
