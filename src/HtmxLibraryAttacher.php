<?php

declare(strict_types=1);

namespace Drupal\htmx_plus;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HtmxLibraryAttacher.
 *
 * Attaches the HTMX library to the given attachments array if it exists
 * and conditions are met.
 *
 * @package Drupal\htmx_plus
 */
class HtmxLibraryAttacher {

  /**
   * The name of the HTMX module.
   *
   * @var string
   */
  private const HTMX_MODULE_NAME = 'htmx';

  /**
   * The name of the HTMX library.
   *
   * @var string
   */
  private const HTMX_LIBRARY_NAME = 'drupal';

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   *   The logger.
   */
  private LoggerInterface $logger;

  public function __construct(
    private LibraryDiscoveryInterface $libraryDiscovery,
    private HtmxConditionVerifier $htmxConditionVerifier,
    private LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->libraryDiscovery = $libraryDiscovery;
    $this->htmxConditionVerifier = $htmxConditionVerifier;
    $this->logger = $this->loggerFactory->get('htmx_plus');
  }

  /**
   * Manages the htmx library for Drupal.
   *
   * @param array<string,array<string,array<int,string>>> $render_array
   *   The render array.
   * @param bool $force_attach
   *   Whether to force attaching the library regardless of conditions.
   */
  public function attachLibraryIfAvailable(array &$render_array, bool $force_attach = FALSE): void {
    if (FALSE === $this->doesXtmxLibraryExist()) {
      return;
    }

    if (FALSE === $force_attach && FALSE === $this->htmxConditionVerifier->shouldAttachHtmxLibrary()) {
      return;
    }

    $render_array['#attached']['library'][] = sprintf('%s/%s', self::HTMX_MODULE_NAME, self::HTMX_LIBRARY_NAME);
  }

  /**
   * Checks if the htmx library exists.
   *
   * @return bool
   *   TRUE if the library exists, FALSE otherwise.
   */
  protected function doesXtmxLibraryExist(): bool {
    $library_definition = $this->libraryDiscovery->getLibraryByName(self::HTMX_MODULE_NAME, self::HTMX_LIBRARY_NAME);
    if (FALSE === $library_definition) {
      $this->logger->warning('Library @module/@library does not exist.',
      ['@module' => self::HTMX_MODULE_NAME, '@library' => self::HTMX_LIBRARY_NAME]);
      return FALSE;
    }

    return TRUE;
  }

}
