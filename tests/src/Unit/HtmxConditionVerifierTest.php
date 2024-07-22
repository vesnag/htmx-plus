<?php

declare(strict_types=1);

namespace Drupal\Tests\htmx_plus\Unit;

use Drupal\Core\Path\PathMatcherInterface;
use Drupal\htmx_plus\HtmxConditionVerifier;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the HtmxConditionVerifier class.
 *
 * @coversDefaultClass \Drupal\htmx_plus\HtmxConditionVerifier
 *
 * @group htmx_plus
 */
class HtmxConditionVerifierTest extends UnitTestCase {

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected PathMatcherInterface|MockObject $pathMatcher;

  /**
   * The HtmxConditionVerifier instance.
   *
   * @var \Drupal\htmx_plus\HtmxConditionVerifier
   */
  protected HtmxConditionVerifier $htmxConditionVerifier;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->pathMatcher = $this->createMock(PathMatcherInterface::class);
    $this->htmxConditionVerifier = new HtmxConditionVerifier($this->pathMatcher);
  }

  /**
   * Tests the shouldAttachHtmxLibrary method.
   *
   * @dataProvider shouldAttachHtmxLibraryDataProvider
   */
  public function testShouldAttachHtmxLibrary(bool $isFrontPage, bool $expectedResult): void {
    $this->pathMatcher->expects($this->once())
      ->method('isFrontPage')
      ->willReturn($isFrontPage);

    $result = $this->htmxConditionVerifier->shouldAttachHtmxLibrary();
    $this->assertEquals($expectedResult, $result);
  }

  /**
   * Data provider for the testShouldAttachHtmxLibrary method.
   *
   * @return array<int,array<int,bool>>
   *   An array of test data.
   */
  public function shouldAttachHtmxLibraryDataProvider(): array {
    return [
      [TRUE, TRUE],
      [FALSE, FALSE],
    ];
  }

}
