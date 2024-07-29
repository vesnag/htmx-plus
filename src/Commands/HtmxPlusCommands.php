<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush commands for the HTMX Plus module.
 */
class HtmxPlusCommands extends DrushCommands {

  /**
   * Toggle hx-debug attribute on all htmx elements.
   *
   * @param string $enable
   *   Whether to enable or disable the debug attribute. 'enable' or 'disable'.
   *
   * @command htmx_plus:toggle-debug.
   *
   * @usage htmx_plus:toggle-debug enable
   *   Enable the hx-debug attribute on all htmx elements.
   * @usage htmx_plus:toggle-debug disable
   *   Disable the hx-debug attribute on all htmx elements.
   */
  public function toggleDebug(string $enable): void {
    if ($enable === 'enable') {
      return;
    }
  }

  /**
   * Set or unset the hx-debug attribute on all htmx elements.
   */
  protected function setDebugAttribute(): void {
  }

}
