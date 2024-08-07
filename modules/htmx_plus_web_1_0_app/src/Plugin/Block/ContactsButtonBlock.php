<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a 'ContactsButton' Block.
 */
#[Block(
  id: "contacts_button_block",
  admin_label: new TranslatableMarkup("Get Contacts Button Block"),
  category: new TranslatableMarkup("HTMX Plus Web 1.0 Application"),
)]
class ContactsButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   *
   * @return array<string,mixed>
   *   The render array for the block.
   */
  public function build(): array {
    return [
      '#theme' => 'get_the_contacts_button_block',
      '#button_text' => $this->t('Get The Contacts'),
    ];
  }

}
