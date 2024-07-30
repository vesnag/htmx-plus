<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Context;

use Drupal\htmx_plus\Checker\AttributeChecker;

/**
 * Context to apply attribute checkers.
 */
class AttributeCheckerContext {

  /**
   * The checkers to apply.
   *
   * @var \Drupal\htmx_plus\Checker\AttributeChecker[]
   */
  private array $checkers = [];

  /**
   * Add a checker to the context.
   */
  public function addChecker(AttributeChecker $checker): void {
    $this->checkers[] = $checker;
  }

  /**
   * Apply all checkers to the attributes.
   *
   * @param string[] $attributes
   *   The attributes to modify.
   *
   * @return string[]
   *   The modified attributes.
   */
  public function applyCheckers(array $attributes): array {
    foreach ($this->checkers as $checker) {
      $attributes = $checker->toggleAttribute($attributes);
    }
    return $attributes;
  }

}
