<?php

/**
 * @file
 * Contains \Drupal\domain_access\Plugin\EntityReferenceSelection\DomainSelection.
 */

namespace Drupal\domain_access\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\SelectionBase;

/**
 * Provides specific access control for the domain entity type.
 *
 * @EntityReferenceSelection(
 *   id = "default:domain",
 *   label = @Translation("Domain selection"),
 *   entity_types = {"domain"},
 *   group = "default",
 *   weight = 1
 * )
 */
class DomainSelection extends SelectionBase {

  /**
   * {@inheritdoc}
   */
  public function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);
    $account = \Drupal::currentUser();
    $user = entity_load('user', $account->id());

    // Get all domains
    if ($account->hasPermission('administer domains')) {
      return $query;
    } 

    // Filter domains by the user's assignments.
    $user_domains = domain_access_get_entity_values($user, DOMAIN_ACCESS_USER_FIELD);
    $query->condition('id', $user_domains, 'IN');

    return $query;
  }
}
