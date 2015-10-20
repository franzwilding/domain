<?php

/**
 * @file
 * Contains \Drupal\domain\Plugin\Action\DeleteDomain.
 */

namespace Drupal\domain\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\domain\DomainInterface;

/**
 * Deletes a domain record.
 *
 * @Action(
 *   id = "domain_delete_action",
 *   label = @Translation("Delete domain record"),
 *   type = "domain"
 * )
 */
class DeleteDomain extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute(DomainInterface $domain = NULL) {
    $domain->delete();
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $objects) {
    foreach ($objects as $object) {
      if ($object instanceOf DomainInterface) {
        $object->delete();
      }
    }
  }

}
