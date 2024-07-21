<?php

namespace Drupal\htmx_plus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Random Number' Block.
 *
 * @Block(
 *   id = "random_number_block",
 *   admin_label = @Translation("Random Number Block"),
 *   category = @Translation("HTMX Plus"),
 * )
 */
class RandomNumberBlock extends BlockBase {

  /**
   * Build the random number block.
   *
   * @return array<string,mixed>
   *   The render array for the random number block.
   */
  public function build(): array {
    return [
      '#theme' => 'htmx_plus_build_random_number',
      '#button_text' => $this->t('Get Random Number'),
      '#attached' => [
        'library' => [
          'htmx/drupal',
        ],
      ],
    ];
  }

}
