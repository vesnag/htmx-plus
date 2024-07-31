<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Checker;

/**
 * Interface for attribute checkers.
 */
interface AttributeCheckerInterface {

  /**
   * Add or remove attributes based on the checker logic.
   *
   * @param string[] $attributes
   *   An array of HTMX attributes.
   *
   * @return string[]
   *   An array of HTMX attributes.
   */
  public function toggleAttribute(array $attributes): array;

}
