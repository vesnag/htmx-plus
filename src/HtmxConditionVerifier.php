<?php

declare(strict_types=1);

namespace Drupal\htmx_plus;

use Drupal\Core\Path\PathMatcherInterface;

/**
 * Checks conditions to determine if the HTMX library should be added.
 *
 * This class provides functionality to check various conditions
 * to decide whether the HTMX library needs to be included in the response.
 *
 * @package Drupal\htmx_plus
 */
class HtmxConditionVerifier {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  public function __construct(PathMatcherInterface $pathMatcher) {
    $this->pathMatcher = $pathMatcher;
  }

  /**
   * Determines if the HTMX library should be attached based on spec conditions.
   *
   * @return bool
   *   True if the HTMX library should be attached, false otherwise.
   */
  public function shouldAttachHtmxLibrary() {
    if ($this->pathMatcher->isFrontPage()) {
      return TRUE;
    }

    return FALSE;
  }

}
