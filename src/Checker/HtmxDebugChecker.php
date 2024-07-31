<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Checker;

use Drupal\htmx_plus\Service\ConfigService;

/**
 * Service to check if debug is enabled.
 */
class HtmxDebugChecker implements AttributeCheckerInterface {

  public function __construct(
    private ConfigService $configService,
  ) {}

  /**
   * Add debug attribute if debug is enabled.
   *
   * @param string[] $attributes
   *   An array of HTMX attributes.
   *
   * @return string[]
   *   An array of HTMX attributes.
   */
  public function toggleAttribute(array $attributes): array {
    if (FALSE === $this->configService->isDebugEnabled()) {
      if (isset($attributes['ext']) && $attributes['ext'] === 'debug') {
        unset($attributes['ext']);
      }
      return $attributes;
    }

    if (TRUE === $this->isDebugAllEnabled()) {
      $attributes['ext'] = 'debug';
    }

    return $attributes;
  }

  /**
   * Check if debug is enabled in settings and state.
   *
   * @return bool
   *   TRUE if debug is enabled, FALSE otherwise.
   */
  private function isDebugAllEnabled(): bool {
    return $this->configService->isDebugEnabled() && $this->configService->isDebugAllEnabled();
  }

}
