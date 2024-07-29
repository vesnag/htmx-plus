<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for state forms.
 *
 * @phpstan-consistent-constructor
 */
abstract class StateFormBase extends ConfigFormBase {

  /**
   * Constructs a new StateFormBase object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(protected StateInterface $state) {
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('state')
    );
  }

}
