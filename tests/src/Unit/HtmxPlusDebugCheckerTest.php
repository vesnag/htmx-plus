<?php

declare(strict_types=1);

use Drupal\htmx_plus\Service\ConfigService;
use Drupal\htmx_plus\Service\HtmxDebugChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test the HtmxDebugChecker service.
 *
 * @coversDefaultClass \Drupal\htmx_plus\Service\HtmxDebugChecker
 */
class HtmxPlusDebugCheckerTest extends TestCase {

  /**
   * The config service.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\Drupal\htmx_plus\Service\ConfigService
   */
  private MockObject|ConfigService $configService;

  /**
   * The htmx debug checker.
   *
   * @var \Drupal\htmx_plus\Service\HtmxDebugChecker
   */
  private HtmxDebugChecker $htmxDebugChecker;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->configService = $this->createMock(ConfigService::class);
    $this->htmxDebugChecker = new HtmxDebugChecker($this->configService);
  }

  /**
   * @covers ::toggleDebugAttribute
   */
  public function testToggleDebugAttributeWhenDebugIsDisabled(): void {
    $this->configService->method('isDebugEnabled')->willReturn(FALSE);

    $attributes = ['ext' => 'debug'];
    $result = $this->htmxDebugChecker->toggleDebugAttribute($attributes);

    $this->assertArrayNotHasKey('ext', $result);
  }

  /**
   * @covers ::toggleDebugAttribute
   */
  public function testToggleDebugAttributeWhenDebugIsEnabledButNotForAll(): void {
    $this->configService->method('isDebugEnabled')->willReturn(TRUE);
    $this->configService->method('isDebugAllEnabled')->willReturn(FALSE);

    $attributes = ['foo' => 'bar'];
    $result = $this->htmxDebugChecker->toggleDebugAttribute($attributes);

    $this->assertArrayNotHasKey('ext', $result);
  }

  /**
   * @covers ::toggleDebugAttribute
   */
  public function testToggleDebugAttributeWhenDebugIsEnabledForAll(): void {
    $this->configService->method('isDebugEnabled')->willReturn(TRUE);
    $this->configService->method('isDebugAllEnabled')->willReturn(TRUE);

    $attributes = ['foo' => 'bar'];
    $result = $this->htmxDebugChecker->toggleDebugAttribute($attributes);

    $this->assertArrayHasKey('ext', $result);
    $this->assertEquals('debug', $result['ext']);
  }

}
