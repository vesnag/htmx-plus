<?php

namespace Drupal\htmx_plus;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HtmxLibraryManager.
 *
 * Manages the htmx library for Drupal.
 *
 * @package Drupal\htmx_plus
 */
class HtmxLibraryManager {

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   *   The logger.
   */
  private LoggerInterface $logger;

  public function __construct(
    private LibraryDiscoveryInterface $libraryDiscovery,
    private HtmxLibraryConditionChecker $htmxLibraryConditionChecker,
    private LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->libraryDiscovery = $libraryDiscovery;
    $this->htmxLibraryConditionChecker = $htmxLibraryConditionChecker;
    $this->logger = $this->loggerFactory->get('htmx_plus');
  }

  /**
   * Manages the htmx library for Drupal.
   *
   * @param array<string,array<string,array<int,string>>> $attachments
   *   The attachments array.
   */
  public function attachHtmxLibraryIfExists(array &$attachments): void {
    if (FALSE === $this->doesXtmxLibraryExist()) {
      return;
    }

    if (FALSE === $this->htmxLibraryConditionChecker->shouldAddHtmxLibrary()) {
      return;
    }

    $attachments['#attached']['library'][] = 'htmx/drupal';
  }

  /**
   * Checks if the htmx library exists.
   *
   * @return bool
   *   TRUE if the library exists, FALSE otherwise.
   */
  protected function doesXtmxLibraryExist(): bool {
    $module_name = 'htmx';
    $library_name = 'drupal';
    $library_definition = $this->libraryDiscovery->getLibraryByName('htmx', 'drupal');
    if (FALSE === $library_definition) {
      $this->logger->warning('Library @module/@library does not exist.',
      ['@module' => $module_name, '@library' => $library_name]);
      return FALSE;
    }

    return TRUE;
  }

}
