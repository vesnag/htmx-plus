<?php

namespace Drupal\htmx_plus\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Service to get configuration values for the HTMX Plus module.
 */
class ConfigService {

  /**
   * Constructs a ConfigService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(protected ConfigFactoryInterface $configFactory) {
  }

  /**
   * Gets the debug_enabled configuration value.
   *
   * @return bool
   *   The debug_enabled configuration value.
   */
  public function isDebugEnabled(): bool {
    $config = $this->configFactory->get('htmx_plus.settings');
    return (bool) $config->get('debug_enabled');
  }

}
