<?php

namespace Drupal\htmx_plus_random_number\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\htmx_plus\HtmxLibraryAttacher;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Random Number' Block.
 */
#[Block(
  id: "random_number_block",
  admin_label: new TranslatableMarkup("Random Number Block"),
  category: new TranslatableMarkup("HTMX Plus Random Number"),
 )]
class RandomNumberBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTMX library attacher service.
   *
   * @var \Drupal\htmx_plus\HtmxLibraryAttacher
   */
  protected $htmxLibraryAttacher;

  /**
   * Constructs a new RandomNumberBlock instance.
   *
   * @param array<int,mixed> $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\htmx_plus\HtmxLibraryAttacher $htmxLibraryAttacher
   *   The HTMX library attacher service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, HtmxLibraryAttacher $htmxLibraryAttacher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->htmxLibraryAttacher = $htmxLibraryAttacher;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container interface.
   * @param array<int,mixed> $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return \Drupal\Core\Plugin\ContainerFactoryPluginInterface|static
   *   The created object.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('htmx_plus.htmx_library_attacher')
    );
  }

  /**
   * Build the random number block.
   *
   * @return array<string,mixed>
   *   The render array for the random number block.
   */
  public function build(): array {
    $build = [
      '#theme' => 'htmx_plus_random_number_build_random_number',
      '#button_text' => $this->t('Get Random Number'),
    ];

    $this->htmxLibraryAttacher->attachLibraryIfAvailable($build, TRUE);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
