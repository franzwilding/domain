<?php

/**
 * @file
 * API documentation file for Domain module.
 */

/**
 * Standard classes and implementations.
 */

/**
 * Notifies other modules that we are loading a domain record from the database.
 *
 * When using this hook, you should invoke the namespace with:
 *
 * use Drupal\domain\DomainInterface;
 *
 *
 * @param array $domain
 *   An array of $domain record objects.
 *
 */
function hook_domain_load(array $domains) {
  // Add a variable to the $domain.
  foreach ($domains as $domain) {
    $domain->addProperty('myvar', 'mydomainvar');
  }
}

/**
 * Allows modules to modify the inbound domain request.
 *
 * To issue a redirect, as in the case of Domain Alias, set a redirect
 * property to a valid response code (301 or 302).
 *
 * @param DomainInterface $domain
 *   A domain object defined by Drupal\domain\DomainInterface.
 */
function hook_domain_request_alter(DomainInterface &$domain) {
  // Add a special case to the example domain.
  if ($domain->id() == 'example_com') {
    // Do something here.
    $domain->addProperty('foo', 'Bar');
  }
}

/**
 * Adds administrative operations for the domain overview form.
 *
 * @param &$operations
 *  An array of links, which uses a unique string key and requires the
 *  elements 'title' and 'href'; the 'query' value is optional, and used
 *  for link-actions with tokens.
 * @param Drupal\domain\DomainInterface
 *   A domain record object.
 *
 * @return array
 *   An array of operations.
 */
function hook_domain_operations(DomainInterface $domain) {
  // Add aliases to the list.
  $id = $domain->id();
  $operations['domain_alias'] = array(
    'title' => t('alias'),
    'href' => "admin/config/domain/$id/alias",
    'query' => array(),
    'weight' => 100, // Core operations start at 0 and increment by 10.
  );
  return $operations;
}

/**
 * Alter the validation step of a domain record.
 *
 * This hook allows modules to change or extend how domain validation
 * happens. Most useful for international domains or other special cases
 * where a site wants to restrict domain creation is some manner.
 *
 * NOTE: This does not apply to Domain Alias records.
 *
 * @param &$error_list
 *   The list of current validation errors. Modify this value by reference.
 *   If you return an empty array or NULL, the domain is considered valid.
 * @param $hostname
 *   The HTTP_HOST string value being validated, such as one.example.com.
 *   Note that this is checked for uniqueness separately. This value is not
 *   modifiable.
 * @return
 *   No return value. Modify $error_list by reference. Return an empty array
 *   or NULL to validate this domain.
 *
 * @see domain_valid_domain()
 */
function hook_domain_validate_alter(&$error_list, $subdomain) {
  // Only allow TLDs to be .org for our site.
  if (substr($subdomain, -4) != '.org') {
    $error_list[] = t('Only .org domains may be registered.');
  }
}
