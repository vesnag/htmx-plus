<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Drush\Commands;

use Drupal\Core\State\StateInterface;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for the HTMX Plus module.
 */
final class HtmxPlusCommands extends DrushCommands {

  use AutowireTrait;

  public function __construct(private StateInterface $state) {}

  /**
   * Toggle ext attribute with value debug on all htmx elements.
   *
   * @param string $mode
   *   The mode to set, either 'on' or 'off'.
   */
  #[CLI\Command(name: 'htmx:debug', aliases: ['htmx-debug'])]
  #[CLI\Argument(name: 'mode', description: 'Mode for enabling and disabling.')]
  #[CLI\Usage(name: 'drush htmx:debug on', description: 'Enable debug mode on all htmx elements.')]
  #[CLI\Usage(name: 'drush htmx:debug off', description: 'Disable debug mode on all htmx elements.')]
  public function toggleDebug(string $mode): void {
    /** @var \Drush\Style\DrushStyle $io */
    $io = $this->io();

    if ($mode === 'on') {
      $this->state->set('htmx_plus.debug_enabled_all', TRUE);
      $io->writeln('Debug enabled for all htmx elements.');
      return;
    }

    if ($mode === 'off') {
      $this->state->set('htmx_plus.debug_enabled_all', FALSE);
      $io->writeln('Debug disabled for all htmx elements.');
      return;
    }

    /** @var \Psr\Log\LoggerInterface $logger */
    $logger = $this->logger();
    $logger->warning('Invalid mode specified. Use "on" or "off".');
  }

}
