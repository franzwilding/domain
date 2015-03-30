<?php

/**
 * @file
 * Definition of Drupal\domain\Tests\DomainEntityReferenceTest.
 */

namespace Drupal\domain\Tests;
use Drupal\domain\DomainInterface;

/**
 * Tests the domain record entity reference field type.
 *
 * @group domain
 */
class DomainEntityReferenceTest extends DomainTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('domain', 'field', 'field_ui');

  /**
   * Create, edit and delete a domain field via the user interface.
   */
  function testDomainField() {
    $this->admin_user = $this->drupalCreateUser(array('administer content types', 'administer node fields', 'administer node display', 'administer domains'));
    $this->drupalLogin($this->admin_user);

    // Visit the article field administration page.
    $this->drupalGet('admin/structure/types/manage/article/fields');
    $this->assertResponse(200, 'Manage fields page accessed.');

    // Check for a domain field.
    $this->assertNoText('Domain test field', 'Domain form field not found.');

    // Visit the article field display administration page.
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertResponse(200, 'Manage field display page accessed.');

    // Check for a domain field.
    $this->assertNoText('Domain test field', 'Domain form field not found.');

    // Create test domain field.
    $this->domainCreateTestField();

    // Visit the article field administration page.
    $this->drupalGet('admin/structure/types/manage/article/fields');

    // Check the new field.
    $this->assertText('Domain test field', 'Added a test field instance.');

    // Visit the article field display administration page.
    $this->drupalGet('admin/structure/types/manage/article/display');

    // Check the new field.
    $this->assertText('Domain test field', 'Added a test field display instance.');
  }

  /**
   * Create content for a domain field.
   */
  function testDomainFieldStorage() {
    $this->admin_user = $this->drupalCreateUser(array('bypass node access', 'administer content types', 'administer node fields', 'administer node display', 'administer domains'));
    $this->drupalLogin($this->admin_user);

    // Create test domain field.
    $this->domainCreateTestField();

    // Create 5 domains.
    $this->domainCreateTestDomains(5);

    // Visit the article field display administration page.
    $this->drupalGet('node/add/article');
    $this->assertResponse(200);

    // Check the new field exists on the page.
    $this->assertText('Domain test field', 'Found the domain field instance.');

    // We expect to find 5 domain options.
    $domains = domain_load_multiple();
    foreach ($domains as $domain) {
      $string = 'value="' . $domain->id() . '"';
      $this->assertRaw($string, format_string('Found the %domain option.', array('%domain' => $domain->label())));
      if (!isset($one)) {
        $one = $domain->id();
        continue;
      }
      if (!isset($two)) {
        $two = $domain->id();
      }
    }

    // Try to post a node, assigned to the first two domains.
    $edit['title[0][value]'] = 'Test node';
    $edit["field_domain[{$one}]"] = TRUE;
    $edit["field_domain[{$two}]"] = TRUE;
    $this->drupalPostForm('node/add/article', $edit, 'Save');
    $this->assertResponse(200);
    $node = node_load(1);
    $values = $node->get('field_domain');

    // @TODO watch for changes in core that affect this test.
    $this->assertTrue(count($values) == 2, 'Node saved with two domain records.');

  }

  /**
   * Creates a simple field for testing on the article content type.
   *
   * @TODO: This code is a model for auto-creation of fields.
   */
  function domainCreateTestField() {
    $label = 'domain';
    $name = 'field_' . $label;

    $storage = array(
      'field_name' => $name,
      'entity_type' => 'node',
      'type' => 'entity_reference',
      'cardinality' => -1,
      'settings' => array(
        'target_type' => 'domain',
      ),
    );
    $field_storage_config = entity_create('field_storage_config', $storage);
    $field_storage_config->save();

    $field = array(
      'field_name' => $name,
      'entity_type' => 'node',
      'label' => 'Domain test field',
      'bundle' => 'article',
      'settings' => array(
        'handler_settings' => array(
          'sort' => array('field' => 'weight', 'direction' => 'ASC'),
        ),
      ),
    );
    $field_config = entity_create('field_config', $field);
    $field_config->save();

    // Tell the form system how to behave.
    entity_get_form_display('node', 'article', 'default')
      ->setComponent($name, array(
        'type' => 'options_buttons',
    ))
    ->save();
  }

}
