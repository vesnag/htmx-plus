<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Test\Mocks;

/**
 * Mock class for the DatabaseQuery class.
 */
class DatabaseQueryMock {

  /**
   * Mock method for setting fields.
   *
   * @param array<string, mixed> $fields
   *   The fields to set.
   *
   * @return self
   *   The current instance.
   */
  public function fields(array $fields): self {
    return $this;
  }

  /**
   * Mock method for setting conditions.
   *
   * @param string $field
   *   The field to set the condition on.
   * @param mixed $value
   *   The value of the condition.
   *
   * @return self
   *   The current instance.
   */
  public function condition(string $field, $value): self {
    return $this;
  }

  /**
   * Mock method for executing the query.
   *
   * @return void
   *   None.
   */
  public function execute(): void {
    // Implementation is not needed for the mock.
  }

}
