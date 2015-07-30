<?php

/**
 * @file
 * Contains \Drupal\domain\Event\DomainContext.
 */

namespace Drupal\domain\EventSubscriber;

use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\domain\DomainNegotiatorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Sets the current user as a context.
 */
class CurrentDomainContext implements ContextProviderInterface {

  use StringTranslationTrait;

  /**
   * @var \Drupal\domain\DomainNegotiatorInterface
   */
  protected $negotiator;

  /**
   * Constructs a DomainContext object.
   *
   * @param \Drupal\domain\DomainNegotiatorInterface $negotiator
   *   The domain negotiator.
   */
  public function __construct(DomainNegotiatorInterface $negotiator) {
    $this->negotiator = $negotiator;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualified_context_ids) {
    $current_domain = $this->negotiator->getActiveDomain();
    $context = new Context(new ContextDefinition('entity:domain', $this->t('Active domain')));
    $context->setContextValue($current_domain);
    return array('domain' => $context);
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts() {
    return $this->getRuntimeContexts([]);
  }
}


