<?php

declare(strict_types=1);

namespace Drupal\htmx_plus;

use Drupal\Core\Path\PathMatcher;

/**
 * Checks conditions to determine if the HTMX library should be added.
 *
 * This class provides functionality to check various conditions
 * to decide whether the HTMX library needs to be included in the response.
 *
 * @package Drupal\htmx_plus
 */
class HtmxLibraryConditionChecker {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  public function __construct(PathMatcher $pathMatcher) {
    $this->pathMatcher = $pathMatcher;
  }

  /**
   * Determines if the library should be added.
   *
   * @return bool
   *   TRUE if the library should be added, otherwise FALSE.
   */
  public function shouldAddHtmxLibrary() {
    if ($this->pathMatcher->isFrontPage()) {
      return TRUE;
    }

    return FALSE;
  }

}
