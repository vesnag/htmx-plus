<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Service;

/**
 * Service to check if debug is enabled.
 */
final class HtmxDebugChecker {

  public function __construct(
    private ConfigService $configService,
  ) {}

  /**
   * Add debug attribute if debug is enabled.
   *
   * @param array<string,string> $attributes
   *   An array of HTMX attributes.
   *
   * @return array<string,string>
   *   An array of HTMX attributes.
   */
  public function toggleDebugAttribute(array $attributes): array {
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
