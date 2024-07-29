<?php

namespace Drupal\htmx_plus\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;

/**
 * Service to get configuration values for the HTMX Plus module.
 */
class ConfigService {

  /**
   * Constructs a ConfigService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(
    private ConfigFactoryInterface $configFactory,
    private StateInterface $state,
  ) {}

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

  /**
   * Gets the debug_enabled_all state value.
   */
  public function isDebugAllEnabled(): bool {
    return (bool) $this->state->get('htmx_plus.debug_enabled_all');
  }

}
