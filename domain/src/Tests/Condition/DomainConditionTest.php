<?php

/**
 * @file
 * Contains \Drupal\domain\Tests\Condition\DomainConditionTest.
 */

namespace Drupal\domain\Tests\Condition;

use Drupal\domain\Tests\DomainTestBase;
use Drupal\Component\Utility\String;
use Drupal\domain\DomainInterface;

/**
 * Tests the domain condition.
 *
 * @group domain
 */
class DomainConditionTest extends DomainTestBase {

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $manager;

  /**
   * A test domain.
   */
  protected $test_domain;

  /**
   * A test domain that never matches the above.
   */
  protected $not_domain;


  /**
   * An array of all domains.
   */
  protected $domains;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Set the condition manager.
    $this->manager = $this->container->get('plugin.manager.condition');

    // Create test domains.
    $this->domainCreateTestDomains(5);

    // Get two sample domains.
    $this->domains = domain_load_multiple();
    $this->test_domain = array_shift($this->domains);
    $this->not_domain = array_shift($this->domains);
  }

  /**
   * Test the domain condition.
   */
  public function testConditions() {
    // Grab the domain condition and configure it to check against one domain.
    $condition = $this->manager->createInstance('domain')
      ->setConfig('domains', array($this->test_domain->id() => $this->test_domain->id()))
      ->setContextValue('domain', $this->not_domain->id());
    $this->assertFalse($condition->execute(), 'Domain request condition fails on wrong domain.');

    // Grab the domain condition and configure it to check against itself.
    $condition = $this->manager->createInstance('domain')
      ->setConfig('domains', array($this->test_domain->id() => $this->test_domain->id()))
      ->setContextValue('domain', $this->test_domain->id());
    $this->assertTrue($condition->execute(), 'Domain request condition succeeds on matching domain.');

    // Check for the proper summary.
    // Summaries require an extra space due to negate handling in summary().
    $this->assertEqual($condition->summary(), 'Active domain is ' . $this->test_domain->label());

    // Check the negated summary.
    $condition->setConfig('negate', TRUE);
    $this->assertEqual($condition->summary(), 'Active domain is not ' . $this->test_domain->label());

    // Check the negated condition.
    $this->assertFalse($condition->execute(), 'Domain request condition fails when condition negated.');
  }

}
